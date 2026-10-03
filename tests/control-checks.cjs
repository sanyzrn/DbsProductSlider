// Exercise controls with real wheel/keyboard input and the actual PHP-generated markup.
module.exports = async function ({ page, setup, params, check, variants, controls, root }) {
    const single = [{ minWidth: 0, slides: 1, gap: 20, group: 1, arrows: true, dots: true }];
    const wheelOptions = { autoplay: false, mousewheel: true, loop: false, rewind: false, profiles: single };
    async function wheel(dx, dy) {
        await page.locator('.pce-v5-slider').first().hover();
        await page.waitForTimeout(100);
        await page.mouse.wheel(dx, dy);
        await page.waitForTimeout(180);
    }
    await page.setViewportSize({ width: 1280, height: 1000 });
    for (const direction of ['ltr', 'rtl']) {
        await setup({ ...wheelOptions, flowDirection: direction }, 10, true);
        await wheel(0, 120);
        check((await params()).index === 1, 'Ordinary vertical mousewheel advances in ' + direction);
        check(await page.locator('.pce-v5-slider').nth(1).evaluate(el => el.swiper.realIndex) === 0, 'Wheel controls only hovered instance');
    }
    await setup(wheelOptions);
    await wheel(120, 0);
    check((await params()).index === 1, 'Horizontal trackpad input advances');
    await setup({ ...wheelOptions, mousewheel: false });
    await wheel(0, 120);
    check((await params()).index === 0, 'Disabled mousewheel cannot advance');
    for (const sensitivity of [0.1, 5]) {
        await setup({ ...wheelOptions, mousewheelSensitivity: sensitivity });
        await wheel(0, 10);
        check((await params()).index === (sensitivity === 5 ? 1 : 0), 'Sensitivity changes actual small-wheel response: ' + sensitivity);
    }
    for (const release of [true, false]) {
        await setup({ ...wheelOptions, mousewheelReleaseOnEdges: release });
        await page.locator('.pce-v5-slider').evaluate(el => el.swiper.slideTo(9, 0));
        await page.waitForTimeout(100);
        const prevented = await page.locator('.pce-v5-slider').evaluate(el => {
            const event = new WheelEvent('wheel', { deltaY: 120, bubbles: true, cancelable: true });
            el.dispatchEvent(event);
            return event.defaultPrevented;
        });
        check(prevented === !release, 'Release-on-edges switch controls page scrolling: ' + release);
    }
    await setup({ ...wheelOptions, loop: true });
    await page.locator('.pce-v5-slider').evaluate(el => el.swiper.slideToLoop(9, 0));
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.realIndex === 9);
    await page.waitForFunction(() => !document.querySelector('.pce-v5-slider').swiper.animating);
    await page.evaluate(() => {
        window.wheelTrace = [];
        const slider = document.querySelector('.pce-v5-slider');
        slider.addEventListener('wheel', e => window.wheelTrace.push({ index: slider.swiper.realIndex, animating: slider.swiper.animating, dx: e.deltaX, dy: e.deltaY }), { capture: true });
        slider.swiper.on('slideChange', () => window.wheelTrace.push({ change: slider.swiper.realIndex }));
    });
    await wheel(0, 120);
    check((await params()).index === 0, 'Wheel wraps when loop is enabled: ' + JSON.stringify(await page.evaluate(() => window.wheelTrace)));
    for (const rewind of [true, false]) {
        await setup({ autoplay: false, loop: false, rewind, profiles: single });
        await page.locator('.pce-v5-slider').evaluate(el => { el.swiper.slideTo(9, 0); el.swiper.slideNext(0); });
        check((await params()).index === (rewind ? 0 : 9), 'Rewind switch changes end-of-carousel behavior');
    }
    await setup({ autoplay: false, keyboard: false });
    await page.locator('.pce-v5-slider').focus();
    await page.keyboard.press('ArrowRight');
    check((await params()).index === 0, 'Disabled keyboard leaves current slide unchanged');
    await setup({ autoplay: false, centeredSlides: true, loop: false });
    const centered = await page.locator('.pce-v5-slider').evaluate(el => {
        const container = el.getBoundingClientRect(), active = el.querySelector('.swiper-slide-active').getBoundingClientRect();
        return Math.abs((container.left + container.right) / 2 - (active.left + active.right) / 2) < 2;
    });
    check(centered, 'Centered slides place the active card in the center');
    await setup({ autoplay: false, dragThreshold: 45, speed: 700, dynamicBullets: true, dynamicMainBullets: 3 });
    check(await page.locator('.pce-v5-slider').evaluate(el => el.swiper.params.threshold === 45 && el.swiper.params.speed === 700 && el.swiper.params.pagination.dynamicMainBullets === 3), 'Threshold, speed and dynamic bullet count reach the engine');
    await page.locator('.swiper-pagination-bullet').nth(1).click();
    await page.waitForTimeout(750);
    check((await params()).index === 3, 'Clickable pagination moves to the selected group');

    await page.mouse.move(0, 0);
    await setup({ autoplay: true, autoplayDelay: 1000, autoplayReverse: true, autoplayPauseOnInteraction: false, pauseOnHover: false, profiles: single });
    await page.waitForFunction(() => document.querySelector('.pce-v5-slider').swiper.realIndex === 9, null, { timeout: 4000 });
    check((await params()).index === 9, 'Actual autoplay honors delay and reverse direction');
    await page.locator('.pce-v5-wrapper').dispatchEvent('mouseenter');
    check(!(await params()).paused, 'Disabled pause-on-hover keeps playing');
    await page.locator('.pce-v5-next').click();
    await page.locator('#outside').focus();
    await page.mouse.move(0, 0);
    await page.waitForTimeout(150);
    check((await params()).running, 'Disabled stop-after-interaction keeps autoplay running');

    await setup({ autoplay: true, testEditor: true });
    check(await page.locator('.pce-v5-autoplay').isVisible() && !(await params()).running, 'Editor previews playback button while autoplay stays stopped');
    await setup({ autoplay: true }, 10, false, false, variants.noButton);
    check(await page.locator('.pce-v5-autoplay').count() === 0 && (await params()).running, 'Hide button removes it without disabling autoplay');
    await setup({ autoplay: false }, 10, false, false, variants.noAutoplay);
    check(await page.locator('.pce-v5-autoplay').count() === 0, 'Autoplay-off markup has no control');
    await setup({ autoplay: true }, 1);
    check(!(await page.locator('.pce-v5-autoplay').isVisible()), 'Single-card playback control stays hidden');
    await setup({ autoplay: true }, 3);
    check(!(await page.locator('.pce-v5-autoplay').isVisible()) && !(await page.locator('.pce-v5-next').isVisible()), 'Locked carousel has no unusable playback or arrow controls');
    await setup({ autoplay: true });
    // Mimic common theme button styles loading after the plugin stylesheet.
    await page.addStyleTag({ content: 'button, [type=button] {background:#bd1318;color:white;border-radius:16px;padding:12px 16px;font-size:16px;}' });
    check(await page.locator('.pce-v5-autoplay').evaluate(el => { const s = getComputedStyle(el); return s.width === '32px' && s.padding === '0px' && s.backgroundColor === 'rgb(255, 255, 255)'; })
        && await page.locator('.pce-v5-next').evaluate(el => getComputedStyle(el).backgroundColor === 'rgba(255, 255, 255, 0.95)'), 'Playback and arrow controls resist global theme button styling');
    check(await page.locator('.pce-v5-autoplay').getAttribute('aria-label') === 'Pause slideshow', 'Icon has accessible pause label');
    await page.locator('.pce-v5-autoplay').click();
    check(await page.locator('.pce-v5-autoplay').getAttribute('aria-label') === 'Play slideshow' && await page.locator('.pce-autoplay-play').isVisible(), 'Pause updates icon and accessible label');
    await page.screenshot({ path: require('node:path').join(root, 'test-results/playback-control.png'), fullPage: true });

    await page.emulateMedia({ reducedMotion: 'reduce' });
    await setup({ autoplay: true });
    check(await page.locator('.pce-v5-card').first().evaluate(el => getComputedStyle(el).transitionDuration) === '0s', 'Reduced motion removes card CSS transitions');
    await setup({ autoplay: true, respectReducedMotion: false }, 10, false, false, variants.motionOptOut);
    check((await params()).running && (await params()).speed === 100, 'Reduced-motion opt-out applies to playback and speed');
    check(await page.locator('.pce-v5-card').first().evaluate(el => getComputedStyle(el).transitionDuration) !== '0s', 'Reduced-motion opt-out applies to CSS as well');
    await page.emulateMedia({ reducedMotion: 'no-preference' });
    await setup({ autoplay: false }, 10, false, false, variants.unequal);
    const heights = await page.locator('.pce-v5-card').evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().height)));
    check(new Set(heights).size > 1, 'Equal-height switch can restore natural card heights');
    await setup({ autoplay: false }, 10, false, false, variants.autoImage);
    check(await page.locator('.pce-v5-media img').first().evaluate(el => { const s = getComputedStyle(el); return Math.abs(parseFloat(s.width) - parseFloat(s.height)) < 2; }), 'Auto image preserves natural aspect ratio');
    for (const preset of ['minimal', 'catalog']) {
        await setup({ autoplay: false }, 10, false, false, variants[preset]);
        check(await page.locator('.pce-v5-card').first().evaluate(el => getComputedStyle(el).boxShadow) === 'none', 'Card preset affects actual CSS: ' + preset);
    }

    // Apply real Elementor selector templates, then check computed styles in Chromium.
    // This validates selector/CSS behavior, not Elementor's editor save/compile pipeline.
    await setup({ autoplay: false, loop: false });
    await page.evaluate(() => { document.body.classList.add('elementor-1'); document.querySelector('main').classList.add('elementor-element', 'elementor-element-fixture'); });
    await page.addStyleTag({ content: 'main * { transition: none !important; }' });
    const cases = [
        ['card_bg', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['card_hover_scale', 1.08, '--pce-card-hover-scale', '1.08'],
        ['card_hover_rotate', 3, '--pce-card-hover-rotate', '3deg'],
        ['card_hover_transition', 700, '--pce-card-transition', '700ms'],
        ['card_image_hover_scale', 1.2, '--pce-card-image-hover-scale', '1.2'],
        ['card_hover_translate', { size: -12, unit: 'px' }, '--pce-card-hover-translate', '-12px'],
        ['card_hover_bg', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['card_hover_border', '#123456', 'borderTopColor', 'rgb(18, 52, 86)'],
        ['image_ratio', '16 / 9', 'aspectRatio', '16 / 9'],
        ['image_fit', 'contain', 'objectFit', 'contain'],
        ['text_align', 'right', 'textAlign', 'right'],
        ['desc_max_lines', 5, 'webkitLineClamp', '5'],
        ['title_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['price_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['desc_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['badge_text_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['badge_bg_color', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['badge_offset_top', { size: 23, unit: 'px' }, 'top', '23px'],
        ['badge_offset_inline', { size: 19, unit: 'px' }, 'insetInlineEnd', '19px'],
        ['badge_icon_position', 'row-reverse', 'flexDirection', 'row-reverse'],
        ['badge_icon_gap', { size: 13, unit: 'px' }, 'columnGap', '13px'],
        ['badge_icon_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['badge_icon_size', { size: 20, unit: 'px' }, 'fontSize', '20px'],
        ['arrow_size', { size: 48, unit: 'px' }, 'width', '48px'],
        ['arrow_icon_size', { size: 26, unit: 'px' }, 'fontSize', '26px'],
        ['arrows_position_top', { size: 75, unit: 'px' }, 'top', '75px'],
        ['arrows_position_sides', { size: -12, unit: 'px' }, 'left', '-12px'],
        ['arrows_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['arrows_bg_color', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['arrows_color_hover', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['arrows_bg_color_hover', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['dots_size', { size: 12, unit: 'px' }, 'height', '12px'],
        ['dots_active_width', { size: 28, unit: 'px' }, 'width', '28px'],
        ['dots_spacing', { size: 8, unit: 'px' }, 'marginRight', '8px'],
        ['dots_top_margin', { size: 16, unit: 'px' }, 'marginTop', '16px'],
        ['dots_alignment', 'right', 'textAlign', 'right'],
        ['dots_color', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['dots_color_active', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['btn_alignment', 'center', 'justifyContent', 'center'],
        ['btn_custom_width', { size: 150, unit: 'px' }, 'width', '150px'],
        ['btn_min_height', { size: 70, unit: 'px' }, 'minHeight', '70px'],
        ['btn_icon_size', { size: 20, unit: 'px' }, 'fontSize', '20px'],
        ['btn_icon_gap', { size: 13, unit: 'px' }, 'columnGap', '13px'],
        ['btn_color', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['btn_bg', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['btn_border_color', '#123456', 'borderTopColor', 'rgb(18, 52, 86)'],
        ['btn_color_hover', '#123456', 'color', 'rgb(18, 52, 86)'],
        ['btn_bg_hover', '#123456', 'backgroundColor', 'rgb(18, 52, 86)'],
        ['btn_border_color_hover', '#123456', 'borderTopColor', 'rgb(18, 52, 86)'],
        ['btn_hover_translate', -6, '--pce-btn-hover-translate', '-6px'],
        ['btn_hover_scale', 1.08, '--pce-btn-hover-scale', '1.08'],
        ['btn_transition_duration', 600, '--pce-btn-transition-duration', '600ms'],
    ];
    for (const name of ['wrapper_padding', 'card_padding', 'badge_padding', 'arrows_wrapper_padding', 'arrows_button_padding', 'btn_padding']) {
        cases.push([name, { top: 7, right: 8, bottom: 9, left: 10, unit: 'px' }, 'paddingLeft', '10px']);
    }
    for (const name of ['wrapper_radius', 'card_radius', 'image_radius', 'badge_radius', 'arrows_radius', 'btn_radius']) {
        cases.push([name, { top: 7, right: 8, bottom: 9, left: 10, unit: 'px' }, 'borderTopLeftRadius', '7px']);
    }
    cases.push(['arrows_button_margin', { top: 7, right: 8, bottom: 9, left: 10, unit: 'px' }, 'marginLeft', '10px']);
    for (const [name, value, property, expected] of cases) {
        const [template, declaration] = Object.entries(controls[name].selectors)[0];
        const selector = template.replaceAll('{{WRAPPER}}', '.elementor-1 .elementor-element.elementor-element-fixture');
        const tokens = typeof value === 'object' ? Object.fromEntries(Object.entries(value).map(([key, val]) => [key.toUpperCase(), val])) : { VALUE: value };
        const css = declaration.replace(/\{\{(\w+)\}\}/g, (_, token) => tokens[token]);
        const style = await page.addStyleTag({ content: selector + '{' + css + '}' });
        if (selector.includes(':hover')) { await page.locator(selector.split(':hover')[0]).first().hover(); }
        const actual = await page.locator(selector).first().evaluate((el, property) => property.startsWith('--') ? getComputedStyle(el).getPropertyValue(property).trim() : getComputedStyle(el)[property], property);
        check(actual === expected, 'Style control changes computed CSS: ' + name + ' (' + actual + ')');
        await style.evaluate(el => el.remove());
    }
};
