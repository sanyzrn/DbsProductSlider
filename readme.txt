=== Product Carousel Elementor ===
Contributors: Saeed Zarrini
Tags: elementor, carousel, slider, products
Requires at least: 6.2
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional product carousel widget for Elementor with advanced effects, deep style controls, and powerful interaction options.

== Description ==

Product Carousel Elementor adds a production-ready product-style carousel widget to Elementor.

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

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the WordPress plugins screen.
3. Make sure Elementor is installed and active.
4. Edit a page with Elementor and search for `Product Carousel Pro` widget.

== Changelog ==

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

= 1.2.1 =
- Added per-item badge icon picker and rendering beside badge label
- Added badge icon styling controls (position, spacing, size, color)
- Fixed button label centering issue by improving button label markup and alignment CSS

= 1.2.0 =
- Added advanced slide direction controls (site auto, LTR, RTL, reverse autoplay)
- Added full button customization suite (alignment, width modes, borders, shadows, normal/hover states, motion)
- Added comprehensive badge styling controls (color, typography, size, position)
- Added extended card hover effects (lift, scale, rotate, image zoom, transition speed, hover shadow/colors)
- Added extensive arrow and pagination customization controls for professional layout precision

= 1.1.0 =
- Full professional refactor of plugin bootstrap, widget controls, and rendering
- Added Elementor/PHP dependency checks with admin notices
- Hardened sanitization and escaping
- Rebuilt JS slider initialization with safer lifecycle handling
- Added arrow and dot controls with accessibility improvements
- Updated visual styling and responsive behavior

== Upgrade Notice ==

= 2.1.0 =
Critical slider interaction bug-fixes plus advanced control refinements and accessibility upgrades.
