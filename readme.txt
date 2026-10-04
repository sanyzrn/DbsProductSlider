=== Product Carousel Elementor ===
Contributors: saeed
Tags: elementor, carousel, slider, products
Requires at least: 6.2
Requires PHP: 7.4
Requires Plugins: elementor
Stable tag: 2.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Responsive product carousels for Elementor with optional WooCommerce sources, Persian UI and RTL support.

== Description ==

Product Carousel Elementor adds manual and optional WooCommerce product carousels to Elementor (3.15 or later).

Features:
- Repeater-based item management
- Responsive slides-per-view and spacing controls
- Advanced slider engine (effects, grouped slides, centered mode, rewind, mousewheel, keyboard)
- Optional autoplay, loop, pause-on-hover, and reverse direction
- Optional arrows and multiple pagination types (bullets, fraction, progressbar)
- Full wrapper, card, image, typography, badge, arrows, dots, and button style controls
- Badge and button icon support with flexible positioning
- Accessible controls and keyboard navigation
- Safe rendering with escaping and sanitization
- Optional WooCommerce latest, featured, sale, category and selected-ID product sources
- Live WooCommerce price markup, stock badges, and product-aware purchase links
- Complete Persian interface and accessible control translations
- Elementor custom breakpoints and independent responsive gaps, groups, arrows and pagination
- Local, pinned, isolated Swiper 11.2.10 assets; no runtime CDN dependency
- Pause/play control, focus/hover handling, and live reduced-motion preference support
- Responsive WordPress attachment images, size/loading controls, and alternative text overrides
- Classic, minimal and catalog presets, component visibility and equal card heights

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the WordPress plugins screen.
3. Make sure Elementor is installed and active.
4. Edit a page with Elementor and search for `Product Carousel Pro` (Persian: اسلایدر محصولات).
5. Select Manual items or WooCommerce products. WooCommerce is optional.

== Frequently Asked Questions ==

= Is WooCommerce required? =
No. Manual items work with Elementor alone. Product sources require active WooCommerce.

= How are products selected? =
Choose latest, featured, sale or selected IDs. Optional category slugs and exclusions apply.
Selected IDs retain their input order; queries return at most 40 cards. Only published,
catalog-visible, non-password-protected products are shown.

= How do purchase buttons work? =
The default opens the product page. Purchase mode uses standard WooCommerce URLs for
available purchasable simple products, option selection for variable products, the
detail page for grouped/unavailable products, and the product URL for external products.
The editor can search products and content by title or SKU to fill selected IDs. This version does not implement AJAX add-to-cart.

= Why can loop turn off? =
Swiper requires enough cards for the active view, effect and group size. If that condition
is not met, rewind is used when enabled. No duplicate or blank product cards are added.

= Will existing pages retain settings? =
The widget name, text domain and old slide counts are retained. The new responsive
Visible Slides control overrides legacy values when set. Regenerate Elementor CSS
and clear page caches after upgrading. See README_FA.md for the staging checklist.

= Are product prices cached? =
This widget bypasses Elementor element output caching. External full-page caches must
still follow your store's existing invalidation policy. No custom price cache is added.

= What was tested? =
PHP regression harnesses and real Swiper in headless Chromium. The harness substitutes
WordPress/Elementor/WooCommerce APIs and does not test a live database or Elementor Editor.
Complete site/editor integration and touch/screen-reader checks are required before broad release.

== Changelog ==

= 2.4.0 =
- Added a WordPress content source: any public content type with optional taxonomy filters, latest or selected IDs, ordering, exclusions and published-only output.
- Added an editor search box (title or SKU) that fills the selected-ID fields for WordPress content and WooCommerce.
- Added a second taxonomy filter and WooCommerce ordering by price, sales and rating.
- Added card link modes (button, title or whole card), an optional second button and custom field mapping for price, second-button link and hover image.
- Added image position and height, hover image, title line limit, full description switch, summary length and counter/progress bar styling.
- Added a Grid layout with responsive columns and gap that does not use the sliding engine.
- Added Persian translations and regression checks for every new control.

= 2.3.2 =
- Added a Show Play/Pause Button switch and a compact, accessible playback icon.
- Previewed the playback control in Elementor while keeping editor autoplay stopped.
- Fixed ordinary vertical mousewheel input and made sensitivity affect snap navigation.
- Applied the reduced-motion switch consistently to JavaScript and decorative CSS.
- Expanded real-browser input and style control checks and refreshed the outstanding audit.

= 2.3.1 =
- Reorganized Content into source, card content, layout, motion, autoplay and navigation sections.
- Grouped technical options in Fine Tuning tabs; retained all existing control IDs and saved values.
- Moved presets, image resolution and description line styling to the Style tab.
- Separated WooCommerce product selection from manual items and shortened responsive help text.

= 2.3.0 =
- Replaced mixed Swiper/CDN dependencies with a pinned, scoped local engine.
- Fixed responsive gaps, groups, custom breakpoints, mobile arrows and safe loop/rewind decisions.
- Preserved card position and autoplay stops across resizes, settings changes and editor markup replacement.
- Added pause/play, focus handling, live reduced motion, focus-scoped keyboard and RTL arrow direction.
- Added optional WooCommerce product selection, official price markup and purchase routes.
- Added Persian translations, responsive attachment images, presets and component visibility.
- Split product data, responsive resolution and rendering; added regression checks and CI.

= 2.1.1 =
- Fixed desktop drag competing with browser text selection and native image/link dragging.
- Kept vertical touch scrolling, pinch zoom, normal links and Swiper's drag click suppression.
- Avoided duplicate initialization when Elementor and document-ready target the same widget.
- Cleaned up drag/hover handlers when the widget is rebuilt in Elementor.
- Prevented hover from restarting autoplay when reduced-motion disabled it.
- Added reduced-motion handling to decorative card/image transitions.

= 2.1.0 =
- Fixed arrow icon centering inside navigation buttons with improved button/icon alignment CSS
- Added arrow spacing controls (wrapper padding, button margin, button padding)
- Fixed drag/swipe instability with safer touch/drag thresholds and interaction defaults
- Fixed mousewheel slide behavior with sensitivity/release-edge controls and stabilized runtime config
- Fixed bullet pagination click behavior with improved clickable/dynamic bullet handling
- Added reduced-motion compatibility option for accessibility and smoother UX
- Added advanced slider tuning controls: drag threshold, dynamic bullets, mousewheel sensitivity

= 2.0.0 =
- Major v2 professional upgrade across controls, rendering, and frontend runtime
- Added advanced slider controls: effects, centered slides, slides-per-group, rewind, mousewheel, keyboard, interaction behavior
- Added image design controls: aspect ratio, object-fit, image border/radius/shadow
- Added wrapper design controls: background, border, radius, spacing
- Added richer button system with per-item icons and extended icon styling controls
- Added flexible pagination modes (bullets/fraction/progressbar) with expanded visual controls
- Added dynamic title tag and description clamp controls for better content structure

= 5.2.1 =
- Added per-item badge icon picker and rendering beside badge label
- Added badge icon styling controls (position, spacing, size, color)
- Fixed button label centering issue by improving button label markup and alignment CSS

= 5.2.0 =
- Added advanced slide direction controls (site auto, LTR, RTL, reverse autoplay)
- Added full button customization suite (alignment, width modes, borders, shadows, normal/hover states, motion)
- Added comprehensive badge styling controls (color, typography, size, position)
- Added extended card hover effects (lift, scale, rotate, image zoom, transition speed, hover shadow/colors)
- Added extensive arrow and pagination customization controls for professional layout precision

= 5.1.0 =
- Full professional refactor of plugin bootstrap, widget controls, and rendering
- Added Elementor/PHP dependency checks with admin notices
- Hardened sanitization and escaping
- Rebuilt JS slider initialization with safer lifecycle handling
- Added arrow and dot controls with accessibility improvements
- Updated visual styling and responsive behavior

== Upgrade Notice ==

= 2.4.0 =
New sources and layout options are additive; saved widgets keep their source, IDs and behavior.
Regenerate Elementor CSS and clear caches after upgrading.

= 2.3.2 =
Playback button visibility is in Content > Autoplay. Mousewheel accepts vertical and
horizontal input. Regenerate Elementor CSS and clear caches after upgrading.

= 2.3.1 =
Editor controls are reorganized. Saved settings retain their IDs, defaults and behavior.
See README_FA.md for the new section map.

= 2.3.0 =
Existing manual data is preserved. Test the upgrade in staging, regenerate Elementor CSS
and clear page caches. WooCommerce remains optional. See README_FA.md.

= 2.1.0 =
Critical slider interaction bug-fixes plus advanced control refinements and accessibility upgrades.
