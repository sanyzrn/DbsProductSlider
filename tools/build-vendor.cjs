// Reproduce the committed, isolated vendor assets from the exact npm dependency.
const fs = require('node:fs');
const path = require('node:path');
const postcss = require('postcss');
const root = path.dirname(require.resolve('swiper/package.json'));
const target = path.join(__dirname, '../assets/vendor/swiper');
fs.mkdirSync(target, { recursive: true });
const js = fs.readFileSync(path.join(root, 'swiper-bundle.min.js'), 'utf8').replace(/\/\/# sourceMappingURL=.*$/m, '');
if (!js.includes('var Swiper=')) throw new Error('Unexpected upstream global export; review before upgrading.');
fs.writeFileSync(path.join(target, 'swiper-bundle.min.js'), '(function () {\n' + js + '\nwindow.PCESwiper = Swiper;\n})();\n');
const css = postcss.parse(fs.readFileSync(path.join(root, 'swiper-bundle.min.css'), 'utf8'));
css.walkRules(rule => {
    if (rule.parent.type === 'atrule' && /keyframes$/i.test(rule.parent.name)) return;
    rule.selectors = rule.selectors.map(selector => selector === ':root' ? '.pce-v5-wrapper' : '.pce-v5-wrapper ' + selector);
});
css.walkComments(comment => { if (comment.text.includes('sourceMappingURL=')) comment.remove(); });
fs.writeFileSync(path.join(target, 'swiper-bundle.min.css'), css.toString().replaceAll('swiper-icons', 'pce-swiper-icons').replaceAll('swiper-preloader-spin', 'pce-swiper-preloader-spin'));
fs.copyFileSync(path.join(root, 'LICENSE'), path.join(target, 'LICENSE'));
console.log('Built isolated Swiper 11.2.10 assets.');
