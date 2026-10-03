# Contributing

Bug reports, focused fixes and documentation improvements are welcome. You can write issues in English or Persian.

## Report a problem

Use the [issue forms](https://github.com/sanyzrn/DbsProductSlider/issues/new/choose). Include plugin, WordPress, Elementor, PHP and optional WooCommerce versions, the relevant settings, and steps to reproduce. Distinguish editor behavior from the public page. Remove private data from logs and screenshots.

Report security issues privately using [the security policy](SECURITY.md).

## Local development

Use Node.js 22+, PHP and PowerShell 7 (`pwsh`) for packaging. Install the development dependencies and browser:

```sh
npm ci
npx playwright install chromium
npm test
```

On Windows, the browser checks use an installed Chrome when available. PHP tests substitute WordPress, Elementor and WooCommerce APIs; run site integration checks when changing their behavior.

When changing vendor assets or translations, rebuild the committed outputs:

```sh
npm run build:vendor
npm run build:translations
```

## Pull requests

Keep changes focused and explain the problem, resulting behavior and validation. Preserve the widget ID `pce_carousel_v5` and saved setting keys. Add regression coverage for functional fixes, and include a screenshot for visible UI changes when useful. Runtime code must remain compatible with PHP 7.4.

See [the release guide](docs/RELEASING.md) for versioning and packaging. Contributions are distributed under the project's GPL-2.0-or-later license.
