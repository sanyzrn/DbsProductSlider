const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const launch = require('./launch.cjs');
const root = path.join(__dirname, '..');
const fixture = fs.readFileSync(path.join(__dirname, '.generated/carousel.html'), 'utf8');
const variants = JSON.parse(fs.readFileSync(path.join(__dirname, '.generated/variants.json'), 'utf8'));
const controls = JSON.parse(fs.readFileSync(path.join(__dirname, '.generated/controls.json'), 'utf8'));
const scripts = {
    jquery: require.resolve('jquery/dist/jquery.js'),
    vendor: path.join(root, 'assets/vendor/swiper/swiper-bundle.min.js'),
    runtime: path.join(root, 'assets/js/script.js')
};
let checks = 0;
function check(value, message) { assert.ok(value, message); checks++; }

(async () => {
    const browser = await launch();
    const page = await browser.newPage({ viewport: { width: 1280, height: 1000 } });
    const errors = [];
    const warnings = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('console', message => { if (message.type() === 'warning') warnings.push(message.text()); });
    await page.route('https://example.test/**', route => route.fulfill({ contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400"><rect width="400" height="400" fill="#e8f1ff"/><circle cx="200" cy="200" r="100" fill="#9cb1cf"/></svg>' }));
    async function setup(overrides = {}, count = 10, multiple = false, delayed = false, markup = fixture) {
        await page.goto('about:blank');
        await page.setContent('<!doctype html><html><body style="margin:40px"><main style="max-width:1100px;margin:auto">' + markup + (multiple ? markup.replaceAll('pce-v5-fixture', 'pce-v5-second') : '') + '</main><button id="outside">Outside</button></body></html>');
        await page.evaluate(({ overrides, count }) => {
            window.Swiper = { sentinel: true };
            window.elementorFrontend = { hooks: { addAction: (name, callback) => { window.reinitialize = callback; } }, isEditMode: () => !!overrides.testEditor };
            if (overrides.testHidden) document.querySelector('main').style.display = 'none';
            document.querySelectorAll('.pce-v5-wrapper').forEach(wrapper => {
                const settings = JSON.parse(wrapper.dataset.settings);
                wrapper.dataset.settings = JSON.stringify({ ...settings, speed: 100, ...overrides });
                Array.from(wrapper.querySelectorAll('.swiper-slide')).slice(count).forEach(card => card.remove());
            });
        }, { overrides, count });
        await page.addStyleTag({ path: path.join(root, 'assets/vendor/swiper/swiper-bundle.min.css') });
        await page.addStyleTag({ path: path.join(root, 'assets/css/style.css') });
        await page.addScriptTag({ path: scripts.jquery });
        if (!delayed) await page.addScriptTag({ path: scripts.vendor });
        await page.addScriptTag({ path: scripts.runtime });
        if (delayed) {
            check(await page.locator('.swiper-slide').first().isVisible(), 'Static fallback cards stay visible');
            await page.addScriptTag({ path: scripts.vendor });
        }
        await page.waitForFunction(() => document.querySelector('.pce-is-ready'));
        await page.waitForFunction(() => Array.from(document.querySelectorAll('.swiper-slide-active img')).every(img => !img.getBoundingClientRect().width || img.complete));
        // Let image/layout observers and Swiper's deferred loop positioning settle.
        await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
        fs.mkdirSync(path.join(root, 'test-results'), { recursive: true });
    }
    async function params() {
        return page.locator('.pce-v5-slider').first().evaluate(slider => ({
            view: slider.swiper.params.slidesPerView, group: slider.swiper.params.slidesPerGroup,
            gap: slider.swiper.params.spaceBetween, loop: slider.swiper.params.loop,
            rewind: slider.swiper.params.rewind, index: slider.swiper.realIndex,
            effect: slider.swiper.params.effect, speed: slider.swiper.params.speed,
            running: slider.swiper.autoplay.running, paused: slider.swiper.autoplay.paused
        }));
    }
    await setup({ autoplay: false });
    check(await page.evaluate(() => window.Swiper.sentinel), 'Elementor Swiper global is untouched');
    check((await params()).group === 3, 'B03: fractional mobile view does not change desktop group');
    const heights = await page.locator('.pce-v5-card').evaluateAll(cards => cards.map(card => Math.round(card.getBoundingClientRect().height)));
    check(new Set(heights).size === 1, 'Equal card heights with unequal descriptions');
    await page.locator('.pce-v5-slider').first().evaluate(slider => { window.originalInstance = slider.swiper; window.reinitialize(window.jQuery(document)); });
    check(await page.evaluate(() => window.originalInstance === document.querySelector('.pce-v5-slider').swiper), 'Duplicate Elementor/ready initialization reuses instance');
    await page.locator('.pce-v5-next').click();
    await page.waitForTimeout(150);
    check((await params()).index === 3, 'Desktop grouped navigation');
    await page.setViewportSize({ width: 900, height: 1000 });
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.params.spaceBetween === 10);
    check((await params()).index === 3 && (await params()).view === 2, 'Tablet gap and index preservation');
    await page.setViewportSize({ width: 390, height: 1000 });
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.params.spaceBetween === 4);
    check((await params()).group === 1 && (await params()).index === 3, 'Mobile gap/group/index preservation');
    check(await page.locator('.pce-v5-next').isVisible(), 'B07: mobile arrows work');
    await page.locator('.pce-v5-next').click();
    await page.waitForTimeout(150);
    check((await params()).index === 4, 'Mobile arrow advances actual Swiper');
    // Rebuild on changed settings and on replacement markup, retaining stable item identity.
    await page.locator('.pce-v5-wrapper').evaluate(wrapper => {
        const settings = JSON.parse(wrapper.dataset.settings);
        settings.speed = 200;
        wrapper.dataset.settings = JSON.stringify(settings);
        window.reinitialize(window.jQuery(document));
    });
    check((await params()).index === 4, 'Settings rebuild preserves current card');
    await page.locator('.pce-v5-wrapper').evaluate(wrapper => {
        const clone = wrapper.cloneNode(true);
        wrapper.replaceWith(clone);
        window.reinitialize(window.jQuery(document));
    });
    check((await params()).index === 4, 'Editor markup replacement preserves current card');
    await page.setViewportSize({ width: 1280, height: 1000 });
    for (const count of [1, 2, 3, 4, 8]) {
        for (const width of [390, 900, 1280]) {
            await page.setViewportSize({ width, height: 1000 });
            await setup({ autoplay: false, profiles: [
                { minWidth: 0, slides: 1.15, gap: 4, group: 1, arrows: true, dots: true },
                { minWidth: 768, slides: 2, gap: 10, group: 1, arrows: true, dots: true },
                { minWidth: 1025, slides: 3, gap: 20, group: 1, arrows: true, dots: true }
            ] }, count);
            const current = await params();
            check(current.loop === (count >= Math.ceil(current.view) + 1), 'Loop eligibility: ' + count + ' cards at ' + width);
            await page.locator('.pce-v5-slider').evaluate(slider => { for (let i = 0; i < 12; i++) slider.swiper.slideNext(0); });
            check(await page.locator('.swiper-slide-blank').count() === 0, 'No blank cards in loop/rewind');
        }
    }
    await page.setViewportSize({ width: 1280, height: 1000 });
    for (const effect of ['slide', 'fade', 'coverflow', 'cards', 'creative']) {
        await setup({ autoplay: false, effect });
        await page.locator('.pce-v5-slider').evaluate(slider => { for (let i = 0; i < 15; i++) slider.swiper.slideNext(0); });
        check((await params()).effect === effect && Number.isFinite((await params()).index), 'Real effect navigation: ' + effect);
    }
    await setup({ autoplay: true });
    await page.mouse.move(0, 0);
    check((await params()).running, 'Autoplay starts when permitted');
    await page.locator('.pce-v5-wrapper').dispatchEvent('mouseenter');
    check((await params()).paused, 'Hover pauses autoplay');
    await page.locator('.pce-v5-wrapper').dispatchEvent('mouseleave');
    check(!(await params()).paused, 'Hover resumes only its own pause');
    await page.locator('.pce-v5-slider').focus();
    check((await params()).paused, 'Focus pauses autoplay');
    await page.locator('#outside').focus();
    check(!(await params()).paused, 'Focus exit resumes when permitted');
    await page.locator('.pce-v5-autoplay').click();
    await page.locator('#outside').focus();
    await page.mouse.move(0, 0);
    await page.locator('.pce-v5-wrapper').dispatchEvent('mouseleave');
    check(!(await params()).running, 'Manual stop survives hover and focus exit');
    await page.locator('.pce-v5-autoplay').click();
    await page.locator('#outside').focus();
    await page.mouse.move(0, 0);
    check((await params()).running, 'Explicit play resumes slideshow');
    await page.locator('.pce-v5-next').click();
    await page.locator('#outside').focus();
    await page.mouse.move(0, 0);
    await page.waitForTimeout(200);
    check(!(await params()).running, 'B06: interaction stop survives hover/focus/transition');
    await page.setViewportSize({ width: 900, height: 1000 });
    await page.waitForTimeout(150);
    check(!(await params()).running, 'Interaction stop survives responsive rebuild');
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.waitForTimeout(150);
    check(!(await params()).running && (await params()).speed === 0 && await page.locator('.pce-v5-autoplay').isDisabled(), 'B05: reduced motion disables autoplay and heavy motion');
    await page.locator('.pce-v5-wrapper').dispatchEvent('mouseleave');
    check(!(await params()).running, 'Reduced motion cannot be bypassed by mouseleave');
    await page.emulateMedia({ reducedMotion: 'no-preference' });
    await page.waitForTimeout(150);
    check(!(await params()).running, 'System preference change preserves interaction stop');
    await page.setViewportSize({ width: 1280, height: 1000 });
    await setup({ autoplay: false, flowDirection: 'rtl' }, 10, true);
    await page.locator('.pce-v5-slider').first().focus();
    await page.keyboard.press('ArrowLeft');
    await page.waitForTimeout(150);
    const indices = await page.locator('.pce-v5-slider').evaluateAll(sliders => sliders.map(slider => slider.swiper.realIndex));
    check(indices[0] === 3 && indices[1] === 0, 'RTL keyboard controls only focused slider');
    check(await page.locator('.pce-v5-prev span').first().evaluate(el => getComputedStyle(el).transform) !== 'none', 'RTL arrow icon mirrors');
    await setup({ autoplay: false, dynamicBullets: true });
    const widths = await page.locator('.swiper-pagination-bullet').evaluateAll(bullets => bullets.map(bullet => getComputedStyle(bullet).width));
    check(new Set(widths).size === 1, 'Dynamic bullets keep identical layout width');
    await setup({ autoplay: false, paginationType: 'fraction' });
    check((await page.locator('.pce-v5-pagination').textContent()).includes('/'), 'Fraction pagination renders');
    await setup({ autoplay: false, paginationType: 'progressbar' });
    check(await page.locator('.swiper-pagination-progressbar-fill').count() === 1, 'Progressbar renders');
    await setup({ autoplay: false, testHidden: true });
    await page.evaluate(() => { document.querySelector('main').style.display = ''; document.querySelector('.pce-v5-slider').swiper.update(); });
    check(await page.locator('.swiper-slide-visible').count() >= 3 && await page.locator('.swiper-slide-visible[inert]').count() === 0, 'Hidden container updates visible card focusability when revealed');
    await setup({ autoplay: true, mousewheel: true });
    await page.locator('.pce-v5-slider').hover();
    await page.waitForTimeout(100); // Swiper intentionally ignores wheel input in its first 60 ms.
    await page.mouse.wheel(150, 0);
    await page.waitForTimeout(150);
    await page.mouse.move(0, 0);
    await page.waitForTimeout(200);
    check(!(await params()).running, 'Mousewheel interaction also preserves autoplay stop');
    await setup({ autoplay: false });
    const dragSurface = await page.locator('.pce-v5-media').nth(2).boundingBox();
    await page.mouse.move(dragSurface.x + dragSurface.width * 0.9, dragSurface.y + 40);
    await page.mouse.down();
    await page.mouse.move(50, dragSurface.y + 40, { steps: 12 });
    await page.mouse.up();
    await page.waitForTimeout(200);
    check((await params()).index > 0, 'Desktop pointer drag advances slider');
    await setup({ autoplay: false, allowTouchMove: false });
    await page.mouse.move(dragSurface.x + dragSurface.width * 0.9, dragSurface.y + 40);
    await page.mouse.down();
    await page.mouse.move(50, dragSurface.y + 40, { steps: 12 });
    await page.mouse.up();
    check((await params()).index === 0, 'Disabled drag leaves index unchanged');
    await setup({ autoplay: false, profiles: [
        { minWidth: 0, slides: 1.15, gap: 4, group: 1, arrows: true, dots: false },
        { minWidth: 1025, slides: 3, gap: 20, group: 1, arrows: false, dots: true }
    ] });
    check(!(await page.locator('.pce-v5-next').isVisible()), 'Desktop responsive arrows can be hidden');
    await page.setViewportSize({ width: 390, height: 1000 });
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.params.spaceBetween === 4);
    check(await page.locator('.pce-v5-next').isVisible() && !(await page.locator('.pce-v5-pagination').isVisible()), 'Mobile arrow/dot settings override desktop');
    await require('./control-checks.cjs')({ page, setup, params, check, variants, controls, root });
    await setup({ autoplay: false });
    await page.setViewportSize({ width: 390, height: 1000 });
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.params.slidesPerView === 1.15);
    const touch = await page.context().newCDPSession(page);
    await touch.send('Emulation.setTouchEmulationEnabled', { enabled: true, maxTouchPoints: 1 });
    await touch.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: 270, y: 150 }] });
    for (let step = 1; step <= 8; step++) {
        await touch.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: 270 - step * 25, y: 150 }] });
    }
    await touch.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
    await page.waitForTimeout(200);
    check((await params()).index > 0, 'Horizontal touch swipe advances a mobile card');
    await page.evaluate(() => { const spacer = document.createElement('div'); spacer.style.height = '1500px'; document.body.appendChild(spacer); });
    await touch.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: 180, y: 320 }] });
    for (let step = 1; step <= 8; step++) {
        await touch.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: 180, y: 320 - step * 25 }] });
    }
    await touch.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
    await page.waitForTimeout(200);
    check(await page.evaluate(() => window.scrollY) > 0, 'Vertical touch gestures still scroll the page');
    await touch.detach();
    const oldInstanceDestroyed = await page.locator('.pce-v5-wrapper').evaluate(wrapper => {
        window.detachedInstance = wrapper.querySelector('.pce-v5-slider').swiper;
        wrapper.remove();
        return true;
    });
    await page.waitForFunction(() => window.detachedInstance.destroyed);
    check(oldInstanceDestroyed, 'Removed widget destroys engine and associated listeners');
    await setup({ autoplay: false }, 10, false, true);
    check(await page.locator('.pce-is-ready').count() === 1, 'Delayed engine loading recovers');
    // Preserve a visual artifact for inspection; no dependency on network image assets.
    await page.setViewportSize({ width: 390, height: 1000 });
    await page.waitForTimeout(150);
    fs.mkdirSync(path.join(root, 'test-results'), { recursive: true });
    await page.screenshot({ path: path.join(root, 'test-results/mobile.png'), fullPage: true });
    check(errors.length === 0, 'No browser exceptions: ' + errors.join('; '));
    check(warnings.filter(warning => warning.includes('Swiper Loop Warning')).length === 0, 'No loop warnings: ' + warnings.join('; '));
    await browser.close();
    console.log('Browser regression checks passed: ' + checks + ' (Chromium ' + browser.version() + ')');
})().catch(error => { console.error(error); process.exit(1); });
