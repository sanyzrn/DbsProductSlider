(function ($) {
    'use strict';
    var controllers = new Map();
    var savedStates = new Map();
    var retryTimer;
    var retries = 0;

    function number(value, fallback, min, max) {
        var parsed = value === '' || value === null ? NaN : Number(value);
        return Number.isFinite(parsed) && parsed >= min && parsed <= max ? parsed : fallback;
    }
    function flag(value, fallback) {
        if (value === true || value === 'yes' || value === 'true' || value === 1) { return true; }
        if (value === false || value === '' || value === 'no' || value === 'false' || value === 0) { return false; }
        return fallback;
    }
    function parse(raw) {
        try {
            var value = JSON.parse(raw);
            return value && !Array.isArray(value) && typeof value === 'object' ? value : {};
        } catch (error) { return {}; }
    }

    function createController(wrapper, settings, key, previousState) {
        var slider = wrapper.querySelector('.pce-v5-slider');
        var nav = wrapper.querySelector('.pce-v5-navigation');
        var dots = wrapper.querySelector('.pce-v5-pagination');
        var toggle = wrapper.querySelector('.pce-v5-autoplay');
        var slideWrapper = slider.querySelector('.swiper-wrapper');
        var cards = Array.from(slideWrapper.children).filter(function (node) { return node.classList.contains('swiper-slide'); });
        var instance;
        var profileKey;
        var resizeFrame;
        var destroyed = false;
        var listeners = [];
        var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
        var manual = previousState ? previousState.manual : false;
        var interacted = previousState ? previousState.interacted : false;
        var hovered = wrapper.matches(':hover');
        var focused = wrapper.contains(document.activeElement);
        var autoplay = flag(settings.autoplay, false);
        var keyboard = flag(settings.keyboard, true);
        var pauseOnHover = flag(settings.pauseOnHover, true);
        var stopOnInteraction = flag(settings.autoplayPauseOnInteraction, false);
        var respectMotion = flag(settings.respectReducedMotion, true);
        var allowDrag = flag(settings.allowTouchMove, true);
        var messages = settings.messages || {};
        var isEditor = window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function' && window.elementorFrontend.isEditMode();
        var profiles = Array.isArray(settings.profiles) && settings.profiles.length ? settings.profiles : [
            { minWidth: 0, slides: settings.mobile || 1.15, gap: settings.gap, group: settings.slidesPerGroup, arrows: settings.showArrows, dots: settings.showDots },
            { minWidth: 768, slides: settings.tablet || 2, gap: settings.gap, group: settings.slidesPerGroup, arrows: settings.showArrows, dots: settings.showDots },
            { minWidth: 1025, slides: settings.desktop || 3, gap: settings.gap, group: settings.slidesPerGroup, arrows: settings.showArrows, dots: settings.showDots }
        ];
        profiles = profiles.slice().sort(function (a, b) { return a.minWidth - b.minWidth; });
        var rtl = settings.flowDirection === 'rtl' || (settings.flowDirection !== 'ltr' && getComputedStyle(wrapper).direction === 'rtl');
        wrapper.setAttribute('dir', rtl ? 'rtl' : 'ltr');
        slider.setAttribute('dir', rtl ? 'rtl' : 'ltr');

        function listen(target, type, handler) {
            target.addEventListener(type, handler);
            listeners.push(function () { target.removeEventListener(type, handler); });
        }
        function reduced() { return respectMotion && media && media.matches; }
        function state() {
            var index = instance && !instance.destroyed ? instance.realIndex : (previousState ? previousState.index : 0);
            return { index: index, card: cards[index] ? cards[index].getAttribute('data-pce-card') : null, manual: manual, interacted: interacted };
        }
        function syncAutoplay() {
            if (!instance || instance.destroyed || !autoplay || !instance.autoplay) { return; }
            var stopped = manual || interacted || reduced() || isEditor || instance.isLocked;
            var paused = focused || (pauseOnHover && hovered) || document.hidden;
            if (stopped) {
                if (instance.autoplay.running) { instance.autoplay.stop(); }
            } else if (paused) {
                if (instance.autoplay.running && !instance.autoplay.paused) { instance.autoplay.pause(); }
            } else if (!instance.autoplay.running) {
                instance.autoplay.start();
                if (instance.autoplay.paused) { instance.autoplay.resume(); }
            } else if (instance.autoplay.paused) {
                instance.autoplay.resume();
            }
            instance.wrapperEl.setAttribute('aria-live', stopped || paused ? 'polite' : 'off');
            if (toggle) {
                toggle.hidden = isEditor || cards.length < 2;
                toggle.disabled = !!reduced() || !!instance.isLocked;
                toggle.textContent = reduced() ? (messages.reduced || 'Slideshow paused: reduced motion')
                    : stopped ? (messages.play || 'Play slideshow') : (messages.pause || 'Pause slideshow');
            }
        }
        function stopAfterInteraction() {
            if (autoplay && stopOnInteraction) { interacted = true; syncAutoplay(); }
        }
        function profile() {
            var selected = profiles[0];
            profiles.forEach(function (candidate) { if (window.innerWidth >= candidate.minWidth) { selected = candidate; } });
            return selected;
        }
        function syncSlides() {
            if (!instance || instance.destroyed) { return; }
            cards.forEach(function (card) {
                var visible = card.classList.contains('swiper-slide-visible') || card.classList.contains('swiper-slide-active');
                if (!visible && card.contains(document.activeElement)) { slider.focus({ preventScroll: true }); }
                card.inert = !visible;
                if (visible) { card.removeAttribute('aria-hidden'); } else { card.setAttribute('aria-hidden', 'true'); }
            });
        }
        function build() {
            if (destroyed) { return; }
            var current = profile();
            var nextKey = JSON.stringify(current) + ':' + !!reduced();
            if (instance && nextKey === profileKey) { return; }
            var snapshot = instance ? state() : previousState;
            if (instance && !instance.destroyed) { instance.destroy(true, true); }
            // Loop reorders real nodes; always restore the source order before rebuilding.
            cards.forEach(function (card) { slideWrapper.appendChild(card); });
            var requestedEffect = ['slide', 'fade', 'coverflow', 'cards', 'creative'].indexOf(settings.effect) >= 0 ? settings.effect : 'slide';
            var effect = reduced() ? 'slide' : requestedEffect;
            var single = effect === 'fade' || effect === 'cards' || effect === 'creative';
            var view = single ? 1 : number(current.slides, 3, 1, 6);
            var group = single || view % 1 !== 0 ? 1 : Math.round(number(current.group, 1, 1, 6));
            var centered = effect === 'cards' || (!single && flag(settings.centeredSlides, false));
            var visible = Math.ceil(view);
            if (centered && visible % 2 === 0) { visible += 1; }
            var looped = centered ? Math.max(group, Math.ceil(visible / 2)) : group;
            looped = Math.ceil(looped / group) * group;
            // Swiper 11.2.10 cards adds three loop slides and requires twice that buffer.
            var minimum = effect === 'cards' ? visible + (looped + 3) * 2 : visible + looped;
            var canLoop = flag(settings.loop, true) && cards.length >= minimum && cards.length % group === 0;
            var initial = snapshot ? snapshot.index : 0;
            if (snapshot && snapshot.card !== null) {
                var found = cards.findIndex(function (card) { return card.getAttribute('data-pce-card') === snapshot.card; });
                if (found >= 0) { initial = found; }
            }
            var paginationType = ['bullets', 'fraction', 'progressbar'].indexOf(settings.paginationType) >= 0 ? settings.paginationType : 'bullets';
            var arrows = flag(current.arrows, true);
            var showDots = flag(current.dots, true);
            var params = {
                init: false, initialSlide: Math.max(0, Math.min(initial, cards.length - 1)),
                direction: 'horizontal', speed: reduced() ? 0 : number(settings.speed, 550, 100, 3000),
                slidesPerView: view, slidesPerGroup: group, spaceBetween: number(current.gap, 20, 0, 60),
                centeredSlides: centered, effect: effect, loop: canLoop, loopAddBlankSlides: false,
                rewind: !canLoop && flag(settings.rewind, true),
                grabCursor: allowDrag, simulateTouch: allowDrag, allowTouchMove: allowDrag,
                threshold: number(settings.dragThreshold, 8, 0, 60),
                touchStartPreventDefault: true, preventClicks: true, preventClicksPropagation: true,
                watchOverflow: true, watchSlidesProgress: true, observer: true, observeParents: true,
                keyboard: { enabled: false },
                a11y: {
                    enabled: true, prevSlideMessage: messages.prev || 'Previous slide',
                    nextSlideMessage: messages.next || 'Next slide', firstSlideMessage: messages.first || 'This is the first slide',
                    lastSlideMessage: messages.last || 'This is the last slide', paginationBulletMessage: messages.bullet || 'Go to slide {{index}}',
                    slideLabelMessage: messages.slide || '{{index}} of {{slidesLength}}', scrollOnFocus: false
                }
            };
            if (effect === 'fade') { params.fadeEffect = { crossFade: true }; }
            if (effect === 'coverflow') { params.coverflowEffect = { rotate: 18, depth: 90, stretch: 0, modifier: 1, slideShadows: false }; }
            if (effect === 'cards') { params.cardsEffect = { slideShadows: false }; }
            if (effect === 'creative') { params.creativeEffect = { prev: { translate: [0, 0, -180] }, next: { translate: ['100%', 0, 0] } }; }
            if (flag(settings.mousewheel, false)) {
                params.mousewheel = { forceToAxis: true, releaseOnEdges: flag(settings.mousewheelReleaseOnEdges, true), sensitivity: number(settings.mousewheelSensitivity, 1, 0.1, 5) };
            }
            if (autoplay) {
                params.autoplay = {
                    enabled: !reduced() && !manual && !interacted && !isEditor,
                    delay: number(settings.autoplayDelay, 3500, 1000, 15000), disableOnInteraction: false,
                    pauseOnMouseEnter: false, reverseDirection: flag(settings.autoplayReverse, false)
                };
            }
            if (nav) {
                nav.classList.toggle('is-hidden', !arrows);
                if (arrows) { params.navigation = { prevEl: nav.querySelector('.pce-v5-prev'), nextEl: nav.querySelector('.pce-v5-next') }; }
            }
            if (dots) {
                dots.classList.toggle('is-hidden', !showDots);
                if (showDots) {
                    params.pagination = {
                        el: dots, type: paginationType, clickable: paginationType === 'bullets',
                        dynamicBullets: paginationType === 'bullets' && flag(settings.dynamicBullets, false),
                        dynamicMainBullets: Math.round(number(settings.dynamicMainBullets, 1, 1, 10)), bulletElement: 'button',
                        renderBullet: function (index, className) { return '<button type="button" class="' + className + '"></button>'; }
                    };
                }
            }
            instance = new window.PCESwiper(slider, params);
            instance.on('sliderFirstMove', stopAfterInteraction);
            instance.on('scroll', stopAfterInteraction);
            instance.on('navigationNext navigationPrev', stopAfterInteraction);
            instance.on('lock unlock', syncAutoplay);
            // Swiper may resume after a transition; enforce persisted stop/pause reasons.
            instance.on('autoplayStart autoplayResume', syncAutoplay);
            instance.on('slideChange transitionEnd update resize observerUpdate', syncSlides);
            instance.init();
            slider.classList.toggle('pce-is-draggable', allowDrag);
            wrapper.classList.add('pce-is-ready');
            profileKey = nextKey;
            syncSlides();
            syncAutoplay();
        }

        listen(wrapper, 'mouseenter', function () { hovered = true; syncAutoplay(); });
        listen(wrapper, 'mouseleave', function () { hovered = false; syncAutoplay(); });
        listen(wrapper, 'focusin', function () { focused = true; syncAutoplay(); });
        listen(wrapper, 'focusout', function (event) { focused = wrapper.contains(event.relatedTarget); syncAutoplay(); });
        listen(document, 'visibilitychange', syncAutoplay);
        listen(wrapper, 'click', function (event) {
            if (toggle && (event.target === toggle || toggle.contains(event.target))) {
                if (reduced()) { return; }
                if (manual || interacted) { manual = false; interacted = false; } else { manual = true; }
                syncAutoplay();
            } else if (event.target.closest('.pce-v5-nav, .swiper-pagination-bullet, .pce-v5-btn')) { stopAfterInteraction(); }
        });
        listen(wrapper, 'keydown', function (event) {
            if (!keyboard || !instance || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey
                || event.target.closest('input, textarea, select, [contenteditable="true"]')) { return; }
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') { return; }
            event.preventDefault();
            stopAfterInteraction();
            if ((event.key === 'ArrowRight') !== rtl) { instance.slideNext(); } else { instance.slidePrev(); }
        });
        listen(slider, 'dragstart', function (event) { if (allowDrag && event.target.closest('img, a')) { event.preventDefault(); } });
        listen(window, 'resize', function () {
            cancelAnimationFrame(resizeFrame);
            resizeFrame = requestAnimationFrame(build);
        });
        if (media) {
            if (media.addEventListener) { listen(media, 'change', build); }
            else { media.addListener(build); listeners.push(function () { media.removeListener(build); }); }
        }
        build();
        return {
            key: key, state: state,
            destroy: function () {
                destroyed = true;
                cancelAnimationFrame(resizeFrame);
                listeners.forEach(function (remove) { remove(); });
                if (instance && !instance.destroyed) { instance.destroy(true, true); }
                cards.forEach(function (card) { card.inert = false; card.removeAttribute('aria-hidden'); slideWrapper.appendChild(card); });
                slider.classList.remove('pce-is-draggable');
                wrapper.classList.remove('pce-is-ready');
            }
        };
    }

    function cleanup() {
        controllers.forEach(function (controller, wrapper) {
            if (!wrapper.isConnected) {
                savedStates.set(wrapper.id, controller.state());
                controller.destroy();
                controllers.delete(wrapper);
            }
        });
        while (savedStates.size > 100) { savedStates.delete(savedStates.keys().next().value); }
    }
    function initialize(scope) {
        cleanup();
        if (typeof window.PCESwiper !== 'function') {
            if (!retryTimer && retries < 20) {
                retryTimer = window.setTimeout(function () { retryTimer = null; retries += 1; initialize(document); }, 250);
            }
            return;
        }
        var root = scope && scope.jquery ? scope[0] : scope;
        if (!root || !root.querySelectorAll) { return; }
        var wrappers = Array.from(root.querySelectorAll('.pce-v5-wrapper'));
        if (root.matches && root.matches('.pce-v5-wrapper')) { wrappers.unshift(root); }
        wrappers.forEach(function (wrapper) {
            var slider = wrapper.querySelector('.pce-v5-slider');
            if (!slider || !slider.querySelector('.swiper-slide')) { return; }
            var key = wrapper.getAttribute('data-settings') || '';
            var existing = controllers.get(wrapper);
            if (existing && existing.key === key) { return; }
            var previousState = existing ? existing.state() : savedStates.get(wrapper.id);
            if (existing) { existing.destroy(); }
            savedStates.delete(wrapper.id);
            controllers.set(wrapper, createController(wrapper, parse(key), key, previousState));
        });
    }
    var hooksRegistered = false;
    function registerHooks() {
        if (!hooksRegistered && window.elementorFrontend && window.elementorFrontend.hooks) {
            window.elementorFrontend.hooks.addAction('frontend/element_ready/pce_carousel_v5.default', initialize);
            hooksRegistered = true;
        }
    }
    $(window).on('elementor/frontend/init', registerHooks);
    $(function () {
        registerHooks();
        initialize(document);
        if (window.MutationObserver) {
            var pending = false;
            new MutationObserver(function (records) {
                var relevant = records.some(function (record) { return !record.target.closest || !record.target.closest('.pce-v5-wrapper'); });
                if (!relevant || pending) { return; }
                pending = true;
                requestAnimationFrame(function () { pending = false; initialize(document); });
            }).observe(document.body, { childList: true, subtree: true });
        }
    });
})(jQuery);
