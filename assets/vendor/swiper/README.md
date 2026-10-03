Swiper **11.2.10**, MIT licensed. Upstream: https://github.com/nolimits4web/swiper/tree/v11.2.10

The bundle is enclosed in a function and exported as `window.PCESwiper`.
Every CSS selector is scoped to `.pce-v5-wrapper`; the font and preloader keyframes are namespaced.
No Elementor/third-party Swiper handle or global is overwritten.

Rebuild with `npm ci` and `npm run build:vendor`. Review upstream changes and run
the browser matrix before changing the pinned version. LICENSE is shipped alongside the assets.
