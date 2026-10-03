(function ($) {
    'use strict';

    function parseSettings(raw) {
        if (!raw || typeof raw !== 'string') {
            return {};
        }

        try {
            return JSON.parse(raw);
        } catch (err) {
            return {};
        }
    }

    function toNumber(value, fallback) {
        var parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    function toBool(value, fallback) {
        if (typeof value === 'boolean') {
            return value;
        }

        if (typeof value === 'string') {
            if (value === 'true' || value === '1' || value === 'yes') {
                return true;
            }
            if (value === 'false' || value === '0' || value === 'no') {
                return false;
            }
        }

        return fallback;
    }

    function initCarousel($scope) {
        var $wrappers = $scope.find('.pce-v5-wrapper');

        if (!$wrappers.length || typeof Swiper === 'undefined') {
            return;
        }

        $wrappers.each(function () {
            var wrapper = this;
            var $wrapper = $(wrapper);
            var sliderEl = wrapper.querySelector('.pce-v5-slider');
            var settings = parseSettings($wrapper.attr('data-settings'));
            var settingsKey = $wrapper.attr('data-settings') || '';

            if (!sliderEl) {
                return;
            }

            if (sliderEl.swiper) {
                // Elementor and document-ready can both initialize the same widget.
                if (!sliderEl.swiper.destroyed && sliderEl.pceSettingsKey === settingsKey) {
                    return;
                }
                sliderEl.swiper.destroy(true, true);
            }

            var slidesDesktop = toNumber(settings.desktop, 3);
            var slidesTablet = toNumber(settings.tablet, 2);
            var slidesMobile = toNumber(settings.mobile, 1.15);
            var gap = toNumber(settings.gap, 20);
            var slidesPerGroup = toNumber(settings.slidesPerGroup, 1);
            var centeredSlides = toBool(settings.centeredSlides, false);
            var effect = settings.effect || 'slide';
            var delay = toNumber(settings.autoplayDelay, 3500);
            var speed = toNumber(settings.speed, 550);
            var loop = toBool(settings.loop, true);
            var rewind = toBool(settings.rewind, true);
            var autoplay = toBool(settings.autoplay, false);
            var autoplayReverse = toBool(settings.autoplayReverse, false);
            var autoplayPauseOnInteraction = toBool(settings.autoplayPauseOnInteraction, false);
            var pauseOnHover = toBool(settings.pauseOnHover, true);
            var showArrows = toBool(settings.showArrows, true);
            var showDots = toBool(settings.showDots, true);
            var paginationType = settings.paginationType || 'bullets';
            var dynamicBullets = toBool(settings.dynamicBullets, false);
            var dynamicMainBullets = toNumber(settings.dynamicMainBullets, 1);
            var mousewheel = toBool(settings.mousewheel, false);
            var mousewheelSensitivity = toNumber(settings.mousewheelSensitivity, 1);
            var mousewheelReleaseOnEdges = toBool(settings.mousewheelReleaseOnEdges, true);
            var keyboard = toBool(settings.keyboard, true);
            var respectReducedMotion = toBool(settings.respectReducedMotion, true);
            var allowTouchMove = toBool(settings.allowTouchMove, true);
            var dragThreshold = toNumber(settings.dragThreshold, 8);
            var flowDirection = settings.flowDirection || 'auto';
            var totalSlides = sliderEl.querySelectorAll('.swiper-slide').length;
            var detectedRtl = document.dir === 'rtl' || $('body').hasClass('rtl');
            var rtl = flowDirection === 'rtl' ? true : flowDirection === 'ltr' ? false : detectedRtl;
            var isSingleSlideEffect = effect === 'fade' || effect === 'cards';
            var hasFractionalSlides = [slidesDesktop, slidesTablet, slidesMobile].some(function (value) {
                return Math.abs(value % 1) > 0.0001;
            });
            var resolvedSlidesPerGroup = Math.max(1, Math.round(slidesPerGroup));

            if ((isSingleSlideEffect || hasFractionalSlides) && resolvedSlidesPerGroup > 1) {
                resolvedSlidesPerGroup = 1;
            }

            var visibleSlides = isSingleSlideEffect ? 1 : Math.ceil(slidesDesktop);

            var canLoop = loop && totalSlides > visibleSlides;
            var reduceMotion = respectReducedMotion && window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            wrapper.setAttribute('dir', rtl ? 'rtl' : 'ltr');
            sliderEl.setAttribute('dir', rtl ? 'rtl' : 'ltr');
            sliderEl.classList.toggle('pce-is-draggable', allowTouchMove);

            var params = {
                direction: 'horizontal',
                speed: speed,
                grabCursor: allowTouchMove,
                simulateTouch: allowTouchMove,
                allowTouchMove: allowTouchMove,
                threshold: Math.max(0, dragThreshold),
                // Let Swiper own mouse dragging instead of the browser's text selection.
                // Swiper still exempts form controls and distinguishes clicks from drags.
                touchStartPreventDefault: true,
                preventClicks: true,
                preventClicksPropagation: true,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                roundLengths: true,
                loop: canLoop,
                rewind: !canLoop && rewind,
                centeredSlides: !isSingleSlideEffect && centeredSlides,
                slidesPerGroup: resolvedSlidesPerGroup,
                effect: effect,
                spaceBetween: gap,
                breakpoints: {
                    0: { slidesPerView: isSingleSlideEffect ? 1 : slidesMobile },
                    768: { slidesPerView: isSingleSlideEffect ? 1 : slidesTablet },
                    1024: { slidesPerView: isSingleSlideEffect ? 1 : slidesDesktop }
                },
                a11y: {
                    enabled: true,
                    prevSlideMessage: 'Previous slide',
                    nextSlideMessage: 'Next slide'
                },
                keyboard: {
                    enabled: keyboard,
                    onlyInViewport: true
                }
            };

            if (mousewheel) {
                params.mousewheel = {
                    forceToAxis: true,
                    releaseOnEdges: mousewheelReleaseOnEdges,
                    sensitivity: Math.max(0.1, mousewheelSensitivity)
                };
            }

            if (effect === 'fade') {
                params.fadeEffect = { crossFade: true };
            } else if (effect === 'coverflow') {
                params.coverflowEffect = {
                    rotate: 18,
                    stretch: 0,
                    depth: 90,
                    modifier: 1,
                    slideShadows: false
                };
            } else if (effect === 'creative') {
                params.creativeEffect = {
                    prev: {
                        shadow: false,
                        translate: [0, 0, -180]
                    },
                    next: {
                        translate: ['100%', 0, 0]
                    }
                };
            }

            if (canLoop && hasFractionalSlides) {
                params.loopedSlides = totalSlides;
                params.loopAdditionalSlides = totalSlides;
            }

            if (reduceMotion) {
                params.speed = Math.min(params.speed, 250);
                params.effect = 'slide';
                delete params.fadeEffect;
                delete params.coverflowEffect;
                delete params.creativeEffect;
            }

            if (autoplay) {
                params.autoplay = {
                    delay: delay,
                    disableOnInteraction: autoplayPauseOnInteraction,
                    pauseOnMouseEnter: false,
                    reverseDirection: autoplayReverse
                };
            }

            if (reduceMotion && params.autoplay) {
                delete params.autoplay;
            }

            if (showArrows) {
                params.navigation = {
                    nextEl: wrapper.querySelector('.pce-v5-next'),
                    prevEl: wrapper.querySelector('.pce-v5-prev')
                };
                $wrapper.find('.pce-v5-navigation').removeClass('is-hidden');
            } else {
                $wrapper.find('.pce-v5-navigation').addClass('is-hidden');
            }

            if (showDots) {
                params.pagination = {
                    el: wrapper.querySelector('.pce-v5-pagination'),
                    type: paginationType,
                    clickable: paginationType === 'bullets' && totalSlides > 1,
                    dynamicBullets: paginationType === 'bullets' ? dynamicBullets : false,
                    dynamicMainBullets: paginationType === 'bullets' ? Math.max(1, Math.round(dynamicMainBullets)) : 1
                };
                $wrapper.find('.pce-v5-pagination').removeClass('is-hidden');
            } else {
                $wrapper.find('.pce-v5-pagination').addClass('is-hidden');
            }

            var instance = new Swiper(sliderEl, params);
            sliderEl.pceSettingsKey = settingsKey;

            function preventNativeDrag(event) {
                if (allowTouchMove && event.target.closest('img, a')) {
                    event.preventDefault();
                }
            }
            sliderEl.addEventListener('dragstart', preventNativeDrag);
            instance.on('destroy', function () {
                sliderEl.removeEventListener('dragstart', preventNativeDrag);
                sliderEl.classList.remove('pce-is-draggable');
                delete sliderEl.pceSettingsKey;
                $wrapper.off('.pceHover');
            });

            $wrapper.off('.pceHover');

            // Reduced-motion may have removed autoplay from the effective settings.
            if (params.autoplay && pauseOnHover && instance.autoplay) {
                $wrapper.on('mouseenter.pceHover', function () {
                    instance.autoplay.stop();
                });

                $wrapper.on('mouseleave.pceHover', function () {
                    instance.autoplay.start();
                });
            }
        });
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/pce_carousel_v5.default', initCarousel);
    });

    $(function () {
        initCarousel($(document));
    });
})(jQuery);
