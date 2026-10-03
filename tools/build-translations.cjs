const fs = require('node:fs');
const path = require('node:path');
const root = path.join(__dirname, '..');
const translations = require('./translations-fa.json');
const files = ['product-carousel-elementor.php', 'widgets/carousel.php', 'includes/class-pce-products.php', 'includes/templates/carousel.php'];
const messages = new Set();
for (const file of files) {
    const source = fs.readFileSync(path.join(root, file), 'utf8');
    const pattern = /(?:__|esc_html__|esc_attr__)\(\s*'((?:\\.|[^'])*)'\s*,\s*'advanced-carousel-pro'/g;
    for (const match of source.matchAll(pattern)) messages.add(match[1].replace(/\\'/g, "'").replace(/\\\\/g, '\\'));
}
const ids = [...messages].sort();
const missing = ids.filter(id => !translations[id]);
if (missing.length) throw new Error('Missing Persian translations: ' + missing.join('; '));
const target = path.join(root, 'languages');
fs.mkdirSync(target, { recursive: true });
const header = 'Project-Id-Version: DbsProductSlider 2.3.0\nLanguage: fa_IR\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nPlural-Forms: nplurals=2; plural=(n > 1);\n';
const entry = (id, value) => (id.includes('%s') ? '#, php-format\n' : '') + 'msgid ' + JSON.stringify(id) + '\nmsgstr ' + JSON.stringify(value) + '\n';
fs.writeFileSync(path.join(target, 'advanced-carousel-pro-fa_IR.po'), entry('', header) + '\n' + ids.map(id => entry(id, translations[id])).join('\n'));
fs.writeFileSync(path.join(target, 'advanced-carousel-pro.pot'), entry('', header.replace('Language: fa_IR', 'Language:')) + '\n' + ids.map(id => entry(id, '')).join('\n'));
const pairs = [['', header], ...ids.map(id => [id, translations[id]])];
const count = pairs.length;
const tables = Buffer.alloc(28 + count * 16);
tables.writeUInt32LE(0x950412de, 0);
tables.writeUInt32LE(count, 8);
tables.writeUInt32LE(28, 12);
tables.writeUInt32LE(28 + count * 8, 16);
const strings = [];
let offset = tables.length;
for (let col = 0; col < 2; col++) {
    pairs.forEach((pair, i) => {
        const value = Buffer.from(pair[col], 'utf8');
        const position = 28 + col * count * 8 + i * 8;
        tables.writeUInt32LE(value.length, position);
        tables.writeUInt32LE(offset, position + 4);
        strings.push(value, Buffer.from([0]));
        offset += value.length + 1;
    });
}
fs.writeFileSync(path.join(target, 'advanced-carousel-pro-fa_IR.mo'), Buffer.concat([tables, ...strings]));
console.log('Built complete Persian translations: ' + ids.length + ' messages.');
