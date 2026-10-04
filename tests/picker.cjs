// Editor search helper: runs assets/js/editor.js against a fake Elementor panel with a mocked endpoint.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const launch = require('./launch.cjs');

(async () => {
    const browser = await launch();
    const page = await browser.newPage();
    const requests = [];
    await page.route('**/admin-ajax.php**', route => {
        requests.push(route.request().url());
        const params = new URL(route.request().url()).searchParams;
        const term = params.get('term');
        if (params.get('ids') !== null) {
            const rows = params.get('ids').split(',').map(id => ({ id: Number(id), title: id === '99' ? '' : 'Item ' + id, available: id !== '99' }));
            return route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true, data: rows }) });
        }
        route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true, data: term === 'none' ? [] : [{ id: 11, title: '<img src=x onerror=window.__xss=1>Gift' }, { id: 12, title: 'Second' }] }) });
    });
    await page.setContent('<div id="elementor-panel"><select data-setting="wp_post_type"><option value="gift" selected>Gift</option></select><input data-setting="wp_selected_ids" value="5"><div id="host"></div></div>');
    await page.addScriptTag({ content: 'window.pceEditor = ' + JSON.stringify({ ajaxUrl: 'https://example.test/wp-admin/admin-ajax.php', action: 'pce_search_content', nonce: 'abc', i18n: { placeholder: 'Search', empty: 'Nothing', error: 'Failed', added: 'Added', remove: 'Remove', unavailable: 'unavailable' } }) });
    await page.addScriptTag({ path: path.join(__dirname, '../assets/js/editor.js') });
    // Elementor renders controls later; the observer must pick them up.
    await page.evaluate(() => { document.getElementById('host').innerHTML = '<div class="pce-picker" data-target="wp_selected_ids" data-type-control="wp_post_type"></div>'; });
    await page.waitForSelector('.pce-picker-input');
    assert.equal(await page.locator('.pce-picker-input').count(), 1, 'Picker initialised once');
    await page.focus('.pce-picker-input');
    await page.waitForSelector('.pce-picker-results button');
    assert.ok(requests[0].includes('post_type=gift') && requests[0].includes('nonce=abc') && requests[0].includes('action=pce_search_content'), 'Request carries type, nonce and action');
    assert.equal(await page.evaluate(() => window.__xss), undefined, 'Titles are rendered as text');
    assert.match(await page.locator('.pce-picker-results button').first().textContent(), /<img src=x/, 'Title shown literally');
    let inputEvents = 0;
    await page.exposeFunction('countInput', () => { inputEvents++; });
    await page.evaluate(() => document.querySelector('[data-setting="wp_selected_ids"]').addEventListener('input', () => window.countInput()));
    await page.locator('.pce-picker-results button').nth(1).click();
    assert.equal(await page.inputValue('[data-setting="wp_selected_ids"]'), '5,12', 'ID appended');
    await page.locator('.pce-picker-results button').nth(1).click();
    assert.equal(await page.inputValue('[data-setting="wp_selected_ids"]'), '5,12', 'Duplicate IDs are not added');
    assert.ok(inputEvents >= 1, 'Elementor input event dispatched');
    // Selected items are listed with titles and can be removed again.
    await page.waitForFunction(() => document.querySelectorAll('.pce-picker-selected li').length === 2);
    assert.match(await page.locator('.pce-picker-selected li').first().textContent(), /Item 5/, 'Selected item shows its title');
    await page.evaluate(() => { document.querySelector('[data-setting="wp_selected_ids"]').value = '5,12,99'; });
    await page.focus('.pce-picker-input');
    await page.waitForFunction(() => document.querySelectorAll('.pce-picker-selected li').length === 3);
    assert.match(await page.locator('.pce-picker-selected li').nth(2).textContent(), /unavailable/, 'Unavailable ID is flagged');
    await page.locator('.pce-picker-selected li').nth(2).locator('button').click();
    assert.equal(await page.inputValue('[data-setting="wp_selected_ids"]'), '5,12', 'Selected ID can be removed');
    await page.waitForFunction(() => document.querySelectorAll('.pce-picker-selected li').length === 2);
    await page.fill('.pce-picker-input', 'none');
    await page.waitForFunction(() => document.querySelector('.pce-picker-note').textContent === 'Nothing');
    await browser.close();
    console.log('Picker checks passed');
})().catch(error => { console.error(error); process.exit(1); });
