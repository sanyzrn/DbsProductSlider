# Nexa Slider

Responsive product and content sliders for Elementor, with manual cards, WordPress content, optional WooCommerce sources, and Persian / RTL support. Free, GPL-licensed, by [Dbs Studio](https://dbsstudio.ir/nexa-slider).

> **Formerly “Product Carousel Elementor” / DbsProductSlider.** The plugin folder and name changed, the saved widget ID did not. See [Upgrading from the old name](#upgrading-from-the-old-name).

[![Checks](https://github.com/sanyzrn/DbsProductSlider/actions/workflows/checks.yml/badge.svg)](https://github.com/sanyzrn/DbsProductSlider/actions/workflows/checks.yml)
[![Release](https://img.shields.io/github/v/release/sanyzrn/DbsProductSlider)](https://github.com/sanyzrn/DbsProductSlider/releases/latest)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)](LICENSE)

**[Download plugin](https://github.com/sanyzrn/DbsProductSlider/releases/latest/download/nexa-slider.zip)** · **[راهنمای فارسی](README_FA.md)** · [Support](https://dbsstudio.ir/nexa-slider) · [Report a bug](https://github.com/sanyzrn/DbsProductSlider/issues/new/choose)

## Features

- Manual cards, any public WordPress content type, or WooCommerce latest, featured, sale, category and selected products.
- Carousel or grid layout, card links, an optional second button and custom field mapping.
- Editor search for selected items and WooCommerce sorting by price, sales and rating.
- Responsive layout, five transition effects, arrows, pagination and drag / swipe.
- Optional autoplay with play / pause, keyboard and mousewheel controls.
- Card presets, component visibility, responsive images and Persian translations.
- A local, isolated Swiper engine with no runtime CDN dependency.

## Install

1. Download the **plugin ZIP** above. GitHub's source archives are intended for development.
2. In WordPress, open **Plugins → Add New → Upload Plugin**, upload the ZIP and activate it.
3. Edit a page in Elementor and add **Nexa Slider**. Choose manual items or WooCommerce products.

Requires WordPress **6.2+**, PHP **7.4+**, and Elementor **3.15+**. WooCommerce is optional.

## Upgrading from the old name

Nexa Slider installs into its own `nexa-slider` folder. Deactivate and delete the earlier *Product Carousel Elementor* plugin, then activate Nexa Slider. Pages keep working because the widget ID (`pce_carousel_v5`) and saved settings are unchanged. If both are active, Nexa Slider stays idle and shows a notice. Regenerate Elementor CSS and clear caches afterwards.

## Development

See [contributing](https://github.com/sanyzrn/DbsProductSlider/blob/main/CONTRIBUTING.md) for local checks and [releasing](https://github.com/sanyzrn/DbsProductSlider/blob/main/docs/RELEASING.md) for automatic versioned releases. Automated checks cover isolated PHP behavior and real Swiper in Chromium; verify the plugin in your WordPress / Elementor setup before production use.

Licensed under [GPL-2.0-or-later](LICENSE). Swiper is bundled under its [MIT license](assets/vendor/swiper/LICENSE).
