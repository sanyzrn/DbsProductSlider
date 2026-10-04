// Shared Chromium launcher. PCE_CHROMIUM_PATH points at an existing binary (Linux/CI containers).
const fs = require('node:fs');
const { chromium } = require('playwright');

module.exports = function launch() {
    const custom = process.env.PCE_CHROMIUM_PATH;
    const windowsChrome = process.platform === 'win32' && 'C:/Program Files/Google/Chrome/Application/chrome.exe';
    if (custom) { return chromium.launch({ executablePath: custom, headless: true, args: ['--no-sandbox'] }); }
    if (windowsChrome && fs.existsSync(windowsChrome)) { return chromium.launch({ executablePath: windowsChrome, headless: true }); }
    return chromium.launch({ headless: true });
};
