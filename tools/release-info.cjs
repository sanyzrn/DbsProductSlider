const fs = require('node:fs');
const path = require('node:path');
const root = path.join(__dirname, '..');
const versionPattern = /^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/;

function parseReleaseInfo(source, readme) {
    const version = source.match(/^\s*\* Version:\s*(\S+)\s*$/m)?.[1];
    const constant = source.match(/private const VERSION = '([^']+)'/)?.[1];
    const stable = readme.match(/^Stable tag:\s*(\S+)\s*$/m)?.[1];
    if (!versionPattern.test(version || '')) throw new Error('Plugin version must be MAJOR.MINOR.PATCH.');
    if (version !== constant || version !== stable) throw new Error('Plugin header, VERSION and Stable tag must match.');
    const changelog = readme.split('== Changelog ==')[1]?.split('== Upgrade Notice ==')[0] || '';
    const section = changelog.split('= ' + version + ' =')[1]?.split(/\n= [^\n]+ =/)[0]?.trim();
    if (!section || !section.startsWith('- ')) throw new Error('A non-empty changelog section is required for ' + version);
    return { version, tag: 'v' + version, changes: section };
}

function readReleaseInfo() {
    return parseReleaseInfo(fs.readFileSync(path.join(root, 'product-carousel-elementor.php'), 'utf8'), fs.readFileSync(path.join(root, 'readme.txt'), 'utf8'));
}

function compareVersions(a, b) {
    if (!versionPattern.test(a) || !versionPattern.test(b)) throw new Error('Invalid release version.');
    const left = a.split('.').map(BigInt), right = b.split('.').map(BigInt);
    for (let i = 0; i < 3; i++) { if (left[i] !== right[i]) return left[i] > right[i] ? 1 : -1; }
    return 0;
}

function releasePlan({ version, sourceSha, mainSha, existingRelease, tagSha, latestVersion }) {
    if (sourceSha !== mainSha) return { needed: false, reason: 'A newer main commit exists; leave publication to its checks.' };
    if (existingRelease && !existingRelease.draft) return { needed: false, reason: 'Version already published; release and assets are unchanged.' };
    if (tagSha && tagSha !== sourceSha) throw new Error('Existing version tag points to a different commit. Do not move it.');
    return { needed: true, latest: !latestVersion || compareVersions(version, latestVersion) > 0 };
}

function lookupRelease(tag, request) {
    const published = request('releases/tags/' + tag, true);
    if (published) return published;
    // The tag endpoint does not resolve an unpublished draft with an uncreated tag.
    for (let page = 1; ; page++) {
        const releases = request('releases?per_page=100&page=' + page);
        const matches = releases.filter(release => release.tag_name === tag);
        if (matches.length > 1) throw new Error('Multiple releases share the candidate tag.');
        if (matches.length) return matches[0];
        if (releases.length < 100) return null;
    }
}

function writeReleaseNotes(info) {
    const notes = `Dbs Product Slider ${info.version}\n\nResponsive Elementor carousels with manual cards, optional WooCommerce sources, Persian / RTL support and a local Swiper engine.\n\n## Changes\n\n${info.changes}\n\n## Installation\n\nDownload \`DbsProductSlider.zip\` or \`DbsProductSlider-${info.version}.zip\` below and upload it in **WordPress → Plugins → Add New → Upload Plugin**. WooCommerce is optional. GitHub's source archives are for development.\n\nRequires WordPress 6.2+, PHP 7.4+ and Elementor 3.15+. After upgrading, regenerate Elementor CSS and clear page caches.\n\n[راهنمای فارسی](https://github.com/sanyzrn/DbsProductSlider/blob/${info.tag}/README_FA.md) · [Changelog](https://github.com/sanyzrn/DbsProductSlider/blob/${info.tag}/readme.txt)\n\nAutomated PHP and Chromium checks pass for this commit. They do not replace full WordPress / Elementor site integration testing.\n`;
    fs.mkdirSync(path.join(root, 'dist'), { recursive: true });
    const filename = path.join(root, 'dist/release-notes.md');
    fs.writeFileSync(filename, notes);
    return filename;
}

module.exports = { parseReleaseInfo, readReleaseInfo, compareVersions, releasePlan, lookupRelease, writeReleaseNotes };
if (require.main === module) { console.log(JSON.stringify(readReleaseInfo(), null, 2)); }
