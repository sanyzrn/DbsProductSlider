// Grid layout: plain CSS, no sliding engine, columns from the stylesheet/Elementor selectors.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const launch = require('./launch.cjs');
const root = path.join(__dirname, '..');
const variants = JSON.parse(fs.readFileSync(path.join(__dirname, '.generated/variants.json'), 'utf8'));
const controls = JSON.parse(fs.readFileSync(path.join(__dirname, '.generated/controls.json'), 'utf8'));
const scripts = {
    jquery: require.resolve('jquery/dist/jquery.js'),
    vendor: path.join(root, 'assets/vendor/swiper/swiper-bundle.min.js'),
    runtime: path.join(root, 'assets/js/script.js')
};

(async () => {
    const browser = await launch();
    const page = await browser.newPage({ viewport: { width: 1200, height: 900 } });
    await page.setContent('<!doctype html><html><body style="margin:40px"><main style="max-width:1100px;margin:auto">' + variants.grid + '</main></body></html>');
    await page.addStyleTag({ path: path.join(root, 'assets/vendor/swiper/swiper-bundle.min.css') });
    await page.addStyleTag({ path: path.join(root, 'assets/css/style.css') });
    await page.addScriptTag({ path: scripts.jquery });
    await page.addScriptTag({ path: scripts.vendor });
    await page.addScriptTag({ path: scripts.runtime });
    await page.waitForTimeout(500);
    assert.equal(await page.locator('.pce-is-ready').count(), 0, 'Grid never starts the sliding engine');
    assert.equal(await page.locator('.swiper-initialized').count(), 0, 'Swiper is not initialised in grid mode');
    const wrapperStyle = await page.locator('.swiper-wrapper').evaluate(el => { const s = getComputedStyle(el); return { display: s.display, columns: s.gridTemplateColumns.split(' ').length, transform: s.transform }; });
    assert.equal(wrapperStyle.display, 'grid');
    assert.equal(wrapperStyle.columns, 3, 'Default of three columns');
    assert.ok(wrapperStyle.transform === 'none' || wrapperStyle.transform === 'matrix(1, 0, 0, 1, 0, 0)', 'No slide transform');
    const cards = await page.locator('.swiper-slide').evaluateAll(els => els.map(el => { const r = el.getBoundingClientRect(); return { x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width) }; }));
    assert.ok(cards.length >= 6, 'Every card stays in the document');
    assert.ok(new Set(cards.map(card => card.y)).size > 1 && new Set(cards.slice(0, 3).map(card => card.y)).size === 1, 'Cards wrap into rows of three');
    assert.ok(cards.every(card => card.w > 100 && card.x >= 0 && card.x + card.w <= 1200), 'All cards are inside the viewport width');
    // The real Elementor selector templates must beat the stylesheet defaults.
    await page.evaluate(() => { document.body.classList.add('elementor-1'); document.querySelector('main').classList.add('elementor-element', 'elementor-element-fixture'); });
    const apply = (control, tokens) => {
        const [template, declaration] = Object.entries(controls[control].selectors)[0];
        const selector = template.replaceAll('{{WRAPPER}}', '.elementor-1 .elementor-element.elementor-element-fixture');
        return selector + '{' + declaration.replace(/\{\{(\w+)\}\}/g, (_, token) => tokens[token]) + '}';
    };
    await page.addStyleTag({ content: apply('grid_columns', { VALUE: 2 }) + apply('grid_gap', { SIZE: 7, UNIT: 'px' }) });
    const tuned = await page.locator('.swiper-wrapper').evaluate(el => { const s = getComputedStyle(el); return { columns: s.gridTemplateColumns.split(' ').length, gap: s.columnGap }; });
    assert.equal(tuned.columns, 2, 'Grid columns control reaches computed CSS');
    assert.equal(tuned.gap, '7px', 'Grid gap control reaches computed CSS');
    assert.equal(await page.locator('.pce-v5-navigation, .swiper-pagination, .pce-v5-autoplay').count(), 0, 'No carousel controls in grid mode');
    // A broken image is replaced once by the placeholder; a broken hover image is dropped.
    await page.evaluate(() => {
        const media = document.querySelector('.pce-v5-media');
        const image = media.querySelector('img');
        image.removeAttribute('data-pce-fallback');
        image.src = 'https://invalid.invalid/missing.png';
        const hover = document.createElement('img');
        hover.className = 'pce-v5-hover-image';
        media.appendChild(hover);
        hover.src = 'https://invalid.invalid/hover.png';
    });
    await page.waitForFunction(() => document.querySelector('.pce-v5-media img').getAttribute('data-pce-fallback') === '1');
    assert.ok((await page.locator('.pce-v5-media img').first().getAttribute('src')).endsWith('placeholder.png'), 'Broken image falls back to the placeholder');
    await page.waitForFunction(() => !document.querySelector('.pce-v5-media .pce-v5-hover-image'));
    // Hover lift and scale add vertical room so lifted cards are not clipped.
    const padding = await page.evaluate(() => {
        const slider = document.querySelector('.pce-v5-slider');
        const base = parseFloat(getComputedStyle(slider).paddingTop);
        slider.style.setProperty('--pce-hover-lift', '-40px');
        slider.style.setProperty('--pce-hover-scale', '1.1');
        const lifted = { top: parseFloat(getComputedStyle(slider).paddingTop), bottom: parseFloat(getComputedStyle(slider).paddingBottom) };
        return { base, ...lifted };
    });
    assert.equal(padding.base, 0, 'Default padding is unchanged by the safe-space rule');
    assert.equal(padding.top, 40 + 20, 'Top padding grows with lift and scale');
    assert.equal(padding.bottom, 20, 'Bottom padding grows with scale');
    await browser.close();
    console.log('Grid checks passed');
})().catch(error => { console.error(error); process.exit(1); });
