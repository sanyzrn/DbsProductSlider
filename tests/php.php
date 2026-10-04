<?php
/** Isolated regression harness; this does not replace WordPress/Elementor integration tests. */
namespace Elementor {
    class Widget_Base {
        public $settings = [];
        public $attributes = [];
        public $controls = [];
        public function get_settings_for_display() { return $this->settings; }
        public function get_data($key) { return $this->settings; }
        public function get_id() { return $this->settings['test_id'] ?? 'fixture'; }
        public function add_render_attribute($key, $name, $value, $overwrite = false) { $this->attributes[$key][$name] = $value; }
        public function remove_render_attribute($key) { unset($this->attributes[$key]); }
        public function get_render_attribute_string($key) {
            $result = [];
            foreach ($this->attributes[$key] as $name => $value) { $result[] = $name . '="' . \esc_attr($value) . '"'; }
            return implode(' ', $result);
        }
        public function add_control($name, $options) { $this->controls[$name] = $options; }
        public function add_responsive_control($name, $options) { $this->add_control($name, $options); }
        public function __call($method, $args) { return []; }
    }
    class Controls_Manager {
        const TEXT = 'text', TEXTAREA = 'textarea', SELECT = 'select', SWITCHER = 'switcher', HIDDEN = 'hidden';
        const MEDIA = 'media', ICONS = 'icons', URL = 'url', REPEATER = 'repeater', NUMBER = 'number';
        const SLIDER = 'slider', DIMENSIONS = 'dimensions', CHOOSE = 'choose', COLOR = 'color';
        const TAB_STYLE = 'style', HEADING = 'heading', RAW_HTML = 'raw_html';
    }
    class Repeater extends Widget_Base { public function get_controls() { return $this->controls; } }
    class Utils { public static function get_placeholder_image_src() { return 'https://example.test/placeholder.png'; } }
    class Icons_Manager { public static function render_icon($value, $attributes) { echo '<svg aria-hidden="true"></svg>'; } }
    class Group_Control_Background { public static function get_type() { return 'background'; } }
    class Group_Control_Border extends Group_Control_Background {}
    class Group_Control_Box_Shadow extends Group_Control_Background {}
    class Group_Control_Typography extends Group_Control_Background {}
    class Plugin { public static $instance; }
}
namespace {
    define('ABSPATH', __DIR__);
    define('ACP_PLUGIN_PATH', dirname(__DIR__) . '/');
    set_error_handler(static function ($severity, $message, $file, $line) { throw new \ErrorException($message, 0, $severity, $file, $line); });
    function __($text, $domain = '') { return $text; }
    function esc_attr($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
    function esc_html($text) { return esc_attr($text); }
    function esc_attr__($text, $domain = '') { return esc_attr($text); }
    function esc_html__($text, $domain = '') { return esc_attr($text); }
    function esc_url_raw($text) { return preg_match('/^(javascript|data|vbscript):/i', trim($text)) ? '' : $text; }
    function esc_url($text) { return esc_attr(esc_url_raw($text)); }
    function wp_json_encode($value) { return json_encode($value); }
    function is_rtl() { return false; }
    function get_post_meta($id, $key, $single) {
        $meta = ['price_field' => '<b>$20</b>', 'brochure' => '77', 'link_field' => 'https://example.test/file.pdf', 'bad_link' => 'javascript:alert(1)', '_hidden' => 'secret'];
        return $key === '_wp_attachment_image_alt' ? 'Media alt' : ($meta[$key] ?? '');
    }
    function is_protected_meta($key, $type) { return $key[0] === '_'; }
    function wp_get_attachment_url($id) { return $id === 77 ? 'https://example.test/uploads/brochure.pdf' : false; }
    function wp_get_attachment_image($id, $size, $icon, $attributes) {
        if ($id === 999) { return ''; }
        return '<img src="https://example.test/' . $size . '.png" width="768" height="768" srcset="https://example.test/small.png 300w, https://example.test/large.png 768w" sizes="(max-width: 768px) 100vw, 768px" alt="' . esc_attr($attributes['alt']) . '"' . (isset($attributes['class']) ? ' class="' . esc_attr($attributes['class']) . '" aria-hidden="' . esc_attr($attributes['aria-hidden'] ?? '') . '"' : '') . ' loading="' . $attributes['loading'] . '" decoding="async" />';
    }
    function wp_get_attachment_image_url($id, $size) { return 'https://example.test/product.png'; }
    function wp_kses_post($html) { return strip_tags($html, '<del><ins><span><bdi>'); }
    function wp_strip_all_tags($html) { return strip_tags($html); }
    function strip_shortcodes($text) { return preg_replace('/\[[^]]+\]/', '', $text); }
    function wp_trim_words($text, $count) { return implode(' ', array_slice(explode(' ', $text), 0, $count)); }
    function sanitize_title($value) { return strtolower(trim($value)); }
    function is_singular($type) { return $GLOBALS['current_product'] > 0; }
    function get_queried_object_id() { return $GLOBALS['current_product']; }
    function get_post_field($field, $id) { if ($field === 'post_excerpt') { return $id === 6 ? 'one two three four five six seven' : 'Excerpt ' . $id; } return $id === 9 || (($GLOBALS['wp_posts'][$id][2] ?? '') !== '') ? ($GLOBALS['wp_posts'][$id][2] ?? 'secret') : ''; }
    function wc_get_product_ids_on_sale() { return $GLOBALS['sale_ids']; }
    $GLOBALS['ajax'] = ['nonce_ok' => true, 'can' => true, 'out' => null];
    function check_ajax_referer($action, $field) { if (!$GLOBALS['ajax']['nonce_ok']) { throw new \RuntimeException('bad nonce'); } return 1; }
    function current_user_can($cap) { return $GLOBALS['ajax']['can'] && $cap === 'edit_posts'; }
    function sanitize_key($v) { return strtolower(preg_replace('/[^a-z0-9_\-]/i', '', $v)); }
    function sanitize_text_field($v) { return trim(strip_tags($v)); }
    function wp_unslash($v) { return $v; }
    function wp_send_json_success($data) { $GLOBALS['ajax']['out'] = ['success' => true, 'data' => $data]; }
    function wp_send_json_error($data, $status) { $GLOBALS['ajax']['out'] = ['success' => false, 'status' => $status]; }
    $GLOBALS['filters'] = [];
    function add_filter($hook, $callback, $priority = 10, $args = 1) { $GLOBALS['filters'][$hook] = $callback; }
    function remove_filter($hook, $callback, $priority = 10) { unset($GLOBALS['filters'][$hook]); }
    function wc_get_products($args) { $GLOBALS['query_args'] = $args; $GLOBALS['filter_active'] = isset($GLOBALS['filters']['woocommerce_product_data_store_cpt_get_products_query']); return $GLOBALS['products']; }
    class Product {
        private $id;
        private $type;
        public function __construct($id, $type = 'simple') { $this->id = $id; $this->type = $type; }
        public function get_id() { return $this->id; }
        public function get_status() { return $this->id === 7 ? 'private' : 'publish'; }
        public function get_catalog_visibility() { return $this->id === 8 ? 'hidden' : 'visible'; }
        public function is_visible() { return $this->id !== 10; }
        public function is_in_stock() { return $this->id !== 6; }
        public function is_purchasable() { return $this->id !== 5; }
        public function is_type($type) { return $this->type === $type; }
        public function get_permalink() { return 'https://example.test/product/' . $this->id; }
        public function add_to_cart_url() { return $this->type === 'external' ? 'https://merchant.test/buy' : '/?add-to-cart=' . $this->id; }
        public function add_to_cart_text() { return $this->type === 'external' ? 'Buy elsewhere' : 'Add to cart'; }
        public function get_name() { return 'Product ' . $this->id; }
        public function get_image_id() { return 42; }
        public function is_on_sale() { return $this->id === 1; }
        public function get_short_description() { return '<p>Product description</p>'; }
        public function get_price_html() { return '<del>200</del><ins><bdi>100</bdi></ins>'; }
    }
    $GLOBALS['current_product'] = 0;
    $GLOBALS['products'] = [];
    $GLOBALS['sale_ids'] = [];
    // WordPress content fixtures: id => [type, status, password, term]. Id 3 is a draft, 4 is protected, 5 is another type.
    $GLOBALS['wp_posts'] = [1 => ['gift', 'publish', '', 'Group A'], 2 => ['gift', 'publish', '', 'Group B'], 3 => ['gift', 'draft', '', 'Group A'], 4 => ['gift', 'publish', 'secret', 'Group A'], 5 => ['page', 'publish', '', ''], 6 => ['gift', 'publish', '', 'Group A']];
    function get_post_types($args = [], $output = 'names') { return ['post' => (object) ['labels' => (object) ['singular_name' => 'Post']], 'gift' => (object) ['labels' => (object) ['singular_name' => 'Gift']], 'attachment' => (object) ['labels' => (object) ['singular_name' => 'Media']]]; }
    function get_taxonomies($args = [], $output = 'names') { return ['gift_group' => (object) ['labels' => (object) ['name' => 'Groups']]]; }
    function taxonomy_exists($taxonomy) { return $taxonomy === 'gift_group'; }
    function is_object_in_taxonomy($type, $taxonomy) { return $type === 'gift' && $taxonomy === 'gift_group'; }
    function wp_get_post_terms($id, $taxonomy, $args) { return [$GLOBALS['wp_posts'][$id][3]]; }
    function get_post_status($id) { return $GLOBALS['wp_posts'][$id][1] ?? false; }
    function get_the_title($id) { return 'Gift ' . $id; }
    function get_post_thumbnail_id($id) { return $id === 2 ? 0 : 42; }
    function get_permalink($id) { return 'https://example.test/gift/' . $id; }
    class WP_Query {
        public $posts = [];
        public function __construct($args) {
            $GLOBALS['wp_query_args'] = $args;
            $ids = $args['post__in'] ?? array_keys($GLOBALS['wp_posts']);
            // A hostile filter may ignore status/password constraints; PCE_Content must still filter.
            foreach ($ids as $id) { if (isset($GLOBALS['wp_posts'][$id]) && $GLOBALS['wp_posts'][$id][0] === $args['post_type'] && !in_array($id, $args['post__not_in'] ?? [], true)) { $this->posts[] = $id; } }
        }
    }
    \Elementor\Plugin::$instance = (object) ['editor' => new class { public function is_edit_mode() { return false; } }];
    require ACP_PLUGIN_PATH . 'includes/class-pce-settings.php';
    require ACP_PLUGIN_PATH . 'includes/class-pce-products.php';
    require ACP_PLUGIN_PATH . 'includes/class-pce-content.php';
    require ACP_PLUGIN_PATH . 'includes/class-pce-search.php';
    require ACP_PLUGIN_PATH . 'widgets/carousel.php';
    $checks = 0;
    function check($condition, $label) {
        global $checks;
        if (!$condition) { throw new \RuntimeException($label); }
        $checks++;
    }
    function render($settings) {
        $widget = new \PCE_Carousel_V5();
        $widget->settings = $settings;
        ob_start();
        $method = new \ReflectionMethod($widget, 'render');
        if (PHP_VERSION_ID < 80100) { $method->setAccessible(true); }
        $method->invoke($widget);
        return ob_get_clean();
    }
    function options($html) {
        preg_match('/data-settings="([^"]*)"/', $html, $matches);
        return json_decode(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'), true);
    }
    $profiles = PCE_Settings::profiles(['space_between' => ['size' => 20], 'space_between_tablet' => ['size' => 10], 'space_between_mobile' => ['size' => 4]]);
    check(array_column($profiles, 'gap') === [4.0, 10.0, 20.0], 'B02: device gaps');
    $profiles = PCE_Settings::profiles(['space_between_tablet' => ['size' => 0], 'space_between_mobile' => ['size' => '']]);
    check($profiles[0]['gap'] === 0.0, 'Empty mobile gap inherits explicit zero');
    $profiles = PCE_Settings::profiles(['slides_desktop' => 4, 'slides_tablet' => 3, 'slides_mobile' => 1.3]);
    check(array_column($profiles, 'slides') === [1.3, 3.0, 4.0], 'Legacy slide settings survive');
    check(PCE_Settings::profiles(['slides_per_view' => 5])[0]['slides'] === 5.0, 'Responsive slide inheritance');
    check(PCE_Settings::profiles(['slides_per_view' => 5, 'slides_tablet' => '', 'slides_mobile' => ''])[0]['slides'] === 5.0, 'Hidden legacy default cannot override inheritance');
    check(PCE_Settings::profiles(['show_arrows' => 'yes', 'show_arrows_mobile' => ''], [])[0]['arrows'], 'Unsaved responsive switcher inherits desktop');
    $points = ['mobile' => [600, 'max'], 'mobile_extra' => [800, 'max'], 'tablet' => [1100, 'max'], 'laptop' => [1400, 'max'], 'widescreen' => [2000, 'min']];
    \Elementor\Plugin::$instance->breakpoints = new class($points) {
        private $points;
        public function __construct($points) { $this->points = $points; }
        public function get_active_breakpoints() {
            $result = [];
            foreach ($this->points as $name => $data) {
                $result[$name] = new class($data) {
                    private $data;
                    public function __construct($data) { $this->data = $data; }
                    public function get_value() { return $this->data[0]; }
                    public function get_direction() { return $this->data[1]; }
                };
            }
            return $result;
        }
    };
    $profiles = PCE_Settings::profiles(['slides_per_view_mobile_extra' => 1.7, 'show_arrows_mobile' => '', 'space_between_widescreen' => ['size' => 30]]);
    check(array_column($profiles, 'minWidth') === [0, 601, 801, 1101, 1401, 2000], 'B08: custom/additional/widescreen breakpoints');
    check($profiles[1]['slides'] === 1.7 && $profiles[0]['arrows'] === false && $profiles[5]['gap'] === 30.0, 'Additional device values');
    unset(\Elementor\Plugin::$instance->breakpoints);
    check(PCE_Settings::number('garbage', 20, 0, 60) === 20, 'Malformed number fallback');
    $item = ['_id' => 'card-0', 'title' => '<script>alert(1)</script>', 'image' => ['id' => 42], 'price' => '<b>100</b>', 'link' => ['url' => 'https://example.test/product', 'is_external' => true, 'custom_attributes' => 'onclick|alert(1)']];
    $html = render(['items' => [$item]]);
    check(options($html)['flowDirection'] === 'auto', 'B09: missing direction, no warning');
    check(strpos($html, '<script>') === false && strpos($html, '&lt;b&gt;100') !== false, 'Manual content is escaped');
    check(strpos($html, 'onclick') === false && strpos($html, 'noopener noreferrer') !== false, 'Safe external link attributes');
    check(strpos($html, 'srcset=') !== false && strpos($html, 'width="768"') !== false, 'Responsive attachment image');
    check(strpos(render(['items' => [$item], 'show_price' => '', 'show_image' => '']), 'pce-v5-price') === false, 'Component visibility');
    $item['link']['url'] = 'javascript:alert(1)';
    $item['image_alt'] = 'Custom alternative';
    check(strpos(render(['items' => [$item]]), 'javascript:') === false, 'Dangerous link rejected');
    check(strpos(render(['items' => [$item]]), 'alt="Custom alternative"') !== false, 'Alt override');
    $item['image']['id'] = 999;
    check(strpos(render(['items' => [$item]]), 'placeholder.png') !== false, 'Missing media fallback');
    check(strpos(render(['items' => [['title' => [], 'price' => [], 'btn_text' => [], 'image' => ['id' => [], 'url' => []], 'link' => ['url' => []]]]]), 'placeholder.png') !== false, 'Malformed imported fields render safely');
    $GLOBALS['products'] = [new Product(7), new Product(8), new Product(9), new Product(6), new Product(1)];
    $cards = PCE_Products::items([]);
    check(count($cards) === 1 && $cards[0]['title'] === 'Product 1', 'Private, hidden, password and out-of-stock products filtered');
    check($GLOBALS['query_args']['status'] === 'publish' && $GLOBALS['query_args']['visibility'] === 'catalog' && $GLOBALS['query_args']['limit'] === 8, 'Bounded public product query');
    $GLOBALS['products'] = [new Product(1), new Product(3), new Product(2)];
    $cards = PCE_Products::items(['product_query' => 'selected', 'selected_product_ids' => '3,1,2', 'product_limit' => 2]);
    check(array_column($cards, 'title') === ['Product 3', 'Product 1'], 'Selected product order before limit');
    check(PCE_Products::items(['product_query' => 'selected']) === [], 'Empty selection cannot expose all products');
    check(PCE_Products::items(['product_query' => 'sale']) === [], 'Empty sale query cannot expose all products');
    foreach (['simple', 'variable', 'grouped', 'external'] as $type) {
        $GLOBALS['products'] = [new Product(1, $type)];
        $card = PCE_Products::items(['product_action' => 'purchase'])[0];
        check($type === 'simple' ? $card['link']['url'] === '/?add-to-cart=1' : ($type === 'external' ? $card['link']['url'] === 'https://merchant.test/buy' : $card['link']['url'] === 'https://example.test/product/1'), 'Correct purchase route: ' . $type);
    }
    $GLOBALS['products'] = [new Product(5)];
    check(PCE_Products::items(['product_action' => 'purchase'])[0]['link']['url'] === 'https://example.test/product/5', 'Unpurchasable simple product uses detail page');
    check(strpos(render(['source' => 'woocommerce']), '<ins>') !== false, 'WooCommerce price markup preserved');
    $GLOBALS['current_product'] = 5;
    check(PCE_Products::items([]) === [], 'Current product excluded');
    $widget = new \PCE_Carousel_V5();
    $controls = new \ReflectionMethod($widget, 'register_controls');
    if (PHP_VERSION_ID < 80100) { $controls->setAccessible(true); }
    $controls->invoke($widget);
    check(isset($widget->controls['slides_desktop']) && isset($widget->controls['slides_per_view']), 'Legacy controls remain registered');
    foreach (['slides_per_view', 'space_between', 'slides_per_group', 'show_arrows', 'show_dots'] as $control) {
        check($widget->controls[$control]['frontend_available'], 'Responsive values survive Elementor control duplication mode: ' . $control);
    }
    $dynamic = new \ReflectionMethod($widget, 'is_dynamic_content');
    if (PHP_VERSION_ID < 80100) { $dynamic->setAccessible(true); }
    check($dynamic->invoke($widget), 'Product output bypasses Elementor element cache');
    check($widget->controls['show_autoplay_button']['default'] === 'yes', 'Playback control remains enabled by default');
    $playback = render(['items' => [$item, $item], 'autoplay' => 'yes']);
    check(strpos($playback, 'class="pce-v5-autoplay"') !== false && strpos($playback, 'aria-label="Pause slideshow"') !== false, 'Playback button has an accessible name');
    check(strpos(render(['items' => [$item], 'autoplay' => 'yes', 'show_autoplay_button' => '']), 'class="pce-v5-autoplay"') === false, 'Playback button can be removed from markup');
    check(strpos(render(['items' => [$item]]), 'class="pce-v5-autoplay"') === false, 'No playback button with autoplay off');
    check(strpos($playback, 'pce-respect-motion') !== false && strpos(render(['items' => [$item], 'respect_reduced_motion' => '']), 'pce-respect-motion') === false, 'Reduced-motion CSS follows the switch');
    $runtime_cases = [
        'autoplay' => ['autoplay', 'yes', true], 'showAutoplayButton' => ['show_autoplay_button', '', false],
        'autoplayDelay' => ['autoplay_delay', 1200, 1200], 'autoplayReverse' => ['autoplay_reverse', 'yes', true],
        'autoplayPauseOnInteraction' => ['autoplay_pause_on_interaction', 'yes', true], 'pauseOnHover' => ['pause_on_hover', '', false],
        'centeredSlides' => ['centered_slides', 'yes', true], 'effect' => ['slider_effect', 'coverflow', 'coverflow'],
        'flowDirection' => ['flow_direction', 'rtl', 'rtl'], 'loop' => ['loop', '', false], 'rewind' => ['rewind', '', false],
        'paginationType' => ['pagination_type', 'fraction', 'fraction'], 'dynamicBullets' => ['dynamic_bullets', 'yes', true],
        'dynamicMainBullets' => ['dynamic_main_bullets', 4, 4], 'speed' => ['transition_speed', 700, 700],
        'allowTouchMove' => ['allow_touch_move', '', false], 'dragThreshold' => ['drag_threshold', 30, 30],
        'mousewheel' => ['mousewheel_control', 'yes', true], 'mousewheelSensitivity' => ['mousewheel_sensitivity', 2, 2],
        'mousewheelReleaseOnEdges' => ['mousewheel_release_on_edges', '', false], 'keyboard' => ['keyboard_control', '', false],
        'respectReducedMotion' => ['respect_reduced_motion', '', false],
    ];
    foreach ($runtime_cases as $output => [$control, $value, $expected]) {
        check(options(render(['items' => [$item], $control => $value]))[$output] === $expected, 'Editor control reaches runtime: ' . $control);
    }
    $full_item = ['title' => 'Visible title', 'price' => '100', 'desc' => 'Description', 'category' => 'Badge', 'image' => ['id' => 42], 'link' => ['url' => '/product']];
    foreach (['image' => 'pce-v5-media', 'title' => 'pce-v5-title', 'price' => 'pce-v5-price', 'description' => 'pce-v5-desc', 'badge' => 'pce-v5-badge', 'button' => 'pce-v5-btn-wrapper'] as $part => $class) {
        check(strpos(render(['items' => [$full_item], 'show_' . $part => '']), $class) === false, 'Card component switch: ' . $part);
    }
    check(strpos(render(['items' => [$full_item], 'title_html_tag' => 'h2']), '<h2 class="pce-v5-title"') !== false, 'Title tag setting');
    check(strpos(render(['items' => [$full_item], 'image_size' => 'large', 'image_loading' => 'eager']), 'large.png') !== false && strpos(render(['items' => [$full_item], 'image_loading' => 'eager']), 'loading="eager"') !== false, 'Image resolution and loading controls');
    $GLOBALS['products'] = [new Product(1), new Product(6)];
    $cards = PCE_Products::items(['product_query' => 'featured', 'product_categories' => 'shoes,bags', 'product_orderby' => 'name', 'product_order' => 'ASC', 'hide_out_of_stock' => '', 'exclude_current_product' => '']);
    check($GLOBALS['query_args']['featured'] && $GLOBALS['query_args']['category'] === ['shoes', 'bags'], 'Featured and category filters reach WooCommerce');
    check($GLOBALS['query_args']['orderby'] === 'name' && $GLOBALS['query_args']['order'] === 'ASC', 'Product sort controls');
    check(count($cards) === 2 && $cards[1]['category'] === 'Out of stock', 'Out-of-stock switch and automatic badge');
    $GLOBALS['sale_ids'] = [1];
    PCE_Products::items(['product_query' => 'sale']);
    check($GLOBALS['query_args']['include'] === [1], 'Sale selection reaches WooCommerce');
    check(PCE_Products::items(['exclude_product_ids' => '1,6', 'exclude_current_product' => '', 'hide_out_of_stock' => '']) === [], 'Excluded product IDs are enforced');
    // WordPress content source (generic post types, no WooCommerce).
    check(array_keys(PCE_Content::post_type_options()) === ['post', 'gift'], 'Content type list is public types without attachments');
    $cards = PCE_Content::items(['wp_post_type' => 'gift']);
    check(array_column($cards, 'title') === ['Gift 1', 'Gift 2', 'Gift 6'], 'Draft and password-protected content never shown');
    check($cards[0]['link']['url'] === 'https://example.test/gift/1' && $cards[0]['desc'] === 'Excerpt 1' && $cards[0]['_id'] === 'wp-gift-1' && $cards[0]['price_html'] === '', 'Card fields come from WordPress API');
    check($GLOBALS['wp_query_args']['post_status'] === 'publish' && $GLOBALS['wp_query_args']['no_found_rows'] === true && $GLOBALS['wp_query_args']['posts_per_page'] === 8, 'Bounded published-only query');
    check(PCE_Content::items(['wp_post_type' => 'secret_type']) === [] && PCE_Content::items(['wp_post_type' => 'attachment']) === [] && PCE_Content::items([]) === [], 'Unlisted content types rejected');
    check($cards[1]['image']['id'] === 0 && strpos(render(['source' => 'wordpress', 'wp_post_type' => 'gift']), 'placeholder.png') !== false, 'Content without image does not break render');
    PCE_Content::items(['wp_post_type' => 'gift', 'wp_limit' => 500]);
    check($GLOBALS['wp_query_args']['posts_per_page'] === 40, 'Oversized limit is clamped to the maximum');
    PCE_Content::items(['wp_post_type' => 'gift', 'wp_limit' => 0]);
    check($GLOBALS['wp_query_args']['posts_per_page'] === 1, 'Too-small limit is clamped to the minimum');
    PCE_Content::items(['wp_post_type' => 'gift', 'wp_limit' => 'abc']);
    check($GLOBALS['wp_query_args']['posts_per_page'] === 8 && PCE_Settings::clamp('', 8, 1, 40) === 8 && PCE_Settings::clamp(INF, 8, 1, 40) === 8 && PCE_Settings::clamp('12', 8, 1, 40) === 12.0, 'Non-numeric limit uses the default');
    $cards = PCE_Content::items(['wp_post_type' => 'gift', 'wp_query' => 'selected', 'wp_selected_ids' => '6,3,1,4,99']);
    check(array_column($cards, 'title') === ['Gift 6', 'Gift 1'] && $GLOBALS['wp_query_args']['orderby'] === 'post__in', 'Selected order kept, unpublished IDs dropped');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_query' => 'selected']) === [], 'Empty selection cannot expose all content');
    PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy' => 'gift_group', 'wp_term_ids' => '7,8', 'wp_orderby' => 'name', 'wp_order' => 'ASC']);
    check($GLOBALS['wp_query_args']['tax_query'][0] === ['taxonomy' => 'gift_group', 'field' => 'term_id', 'terms' => [7, 8]] && $GLOBALS['wp_query_args']['orderby'] === 'title' && $GLOBALS['wp_query_args']['order'] === 'ASC', 'Term filter and sort mapping');
    check(PCE_Content::items(['wp_post_type' => 'post', 'wp_taxonomy' => 'gift_group', 'wp_term_ids' => '7']) === [] && PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy' => 'nope', 'wp_term_ids' => '7']) === [], 'Taxonomy must belong to content type');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy' => 'gift_group'])[0]['category'] === 'Group A' && PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy' => 'gift_group', 'wp_badge' => 'none'])[0]['category'] === '', 'Group badge switch');
    check(array_column(PCE_Content::items(['wp_post_type' => 'gift', 'wp_exclude_ids' => '1,2']), 'title') === ['Gift 6'], 'Excluded IDs enforced');
    $html = render(['source' => 'wordpress', 'wp_post_type' => 'gift', 'wp_button_text' => 'Details']);
    check(strpos($html, 'Gift 1') !== false && strpos($html, 'Details') !== false && strpos($html, 'Gift 3') === false, 'WordPress source renders through widget');
    check(strpos(render(['source' => 'bogus', 'items' => [$full_item]]), 'Visible title') !== false, 'Unknown source falls back to manual');
    // Phase 1: custom fields, second button, card link modes.
    $cards = PCE_Content::items(['wp_post_type' => 'gift', 'wp_price_meta' => 'price_field', 'wp_btn2_text' => 'PDF', 'wp_btn2_meta' => 'brochure']);
    check($cards[0]['price'] === '$20' && $cards[0]['btn2_link']['url'] === 'https://example.test/uploads/brochure.pdf', 'Meta price and media-ID second button');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_price_meta' => '_hidden'])[0]['price'] === '' && PCE_Content::items(['wp_post_type' => 'gift', 'wp_price_meta' => 'a b;c'])[0]['price'] === '', 'Protected or malformed meta keys ignored');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_btn2_text' => 'PDF', 'wp_btn2_meta' => 'link_field'])[0]['btn2_link']['url'] === 'https://example.test/file.pdf' && PCE_Content::items(['wp_post_type' => 'gift', 'wp_btn2_text' => 'PDF', 'wp_btn2_meta' => 'bad_link'])[0]['btn2_link']['url'] === '' && PCE_Content::items(['wp_post_type' => 'gift', 'wp_btn2_meta' => 'link_field'])[0]['btn2_link']['url'] === '', 'Second button URL validated and needs text');
    $html = render(['source' => 'wordpress', 'wp_post_type' => 'gift', 'wp_btn2_text' => 'PDF', 'wp_btn2_meta' => 'link_field']);
    check(substr_count($html, 'pce-v5-btn-secondary') === 3 && strpos($html, 'href="https://example.test/file.pdf"') !== false, 'Second button rendered');
    check(strpos(render(['source' => 'wordpress', 'wp_post_type' => 'gift']), 'pce-v5-btn-secondary') === false, 'No second button without value');
    $linked = ['title' => 'Linked', 'link' => ['url' => 'https://example.test/a'], 'btn2_text' => 'Brochure', 'btn2_link' => ['url' => 'javascript:alert(1)']];
    check(strpos(render(['items' => [$linked]]), 'pce-v5-title-link') === false && strpos(render(['items' => [$linked]]), 'javascript:') === false, 'Default card link and unsafe second URL');
    $html = render(['items' => [$linked], 'card_link' => 'card']);
    check(strpos($html, 'pce-card-linked') !== false && strpos($html, '<a class="pce-v5-title-link" href="https://example.test/a"') !== false, 'Whole-card link mode');
    check(strpos(render(['items' => [['title' => 'No link']], 'card_link' => 'card']), 'pce-v5-title-link') === false && strpos(render(['items' => [$linked], 'card_link' => 'bogus']), 'pce-v5-title-link') === false, 'Card link needs URL and valid mode');
    // Phase 2: sorting, second taxonomy and editor search.
    $GLOBALS['products'] = [new Product(1)];
    foreach (['price' => '_price', 'popularity' => 'total_sales', 'rating' => '_wc_average_rating'] as $orderby => $meta) {
        PCE_Products::items(['product_orderby' => $orderby, 'exclude_current_product' => '']);
        $mapped = PCE_Products::sort_query(['orderby' => 'date'], $GLOBALS['query_args']);
        check($GLOBALS['filter_active'] && $GLOBALS['query_args']['pce_sort'] === $meta && $mapped['meta_key'] === $meta && $mapped['orderby'] === 'meta_value_num', 'WooCommerce sort: ' . $orderby);
        check(!isset($GLOBALS['filters']['woocommerce_product_data_store_cpt_get_products_query']), 'Sort filter removed after query: ' . $orderby);
    }
    PCE_Products::items(['product_orderby' => 'date', 'exclude_current_product' => '']);
    check(!$GLOBALS['filter_active'] && !isset($GLOBALS['query_args']['pce_sort']), 'No sort filter for plain orderings');
    check(PCE_Products::sort_query(['orderby' => 'date'], ['pce_sort' => 'evil_key']) === ['orderby' => 'date'], 'Unlisted sort meta key ignored');
    PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy' => 'gift_group', 'wp_term_ids' => '7', 'wp_taxonomy_2' => 'gift_group', 'wp_term_ids_2' => '9']);
    check($GLOBALS['wp_query_args']['tax_query'][0] === 'AND' || ($GLOBALS['wp_query_args']['tax_query']['relation'] ?? '') === 'AND', 'Two taxonomy filters combine with AND');
    check(count($GLOBALS['wp_query_args']['tax_query']) === 3, 'Both taxonomy clauses present');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy_2' => 'nope', 'wp_term_ids_2' => '9']) === [], 'Second taxonomy validated');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_taxonomy_2' => 'gift_group'])[0]['title'] === 'Gift 1', 'Second taxonomy without terms is ignored');
    check(array_column(PCE_Search::search('gift', ''), 'id') === [1, 2, 6], 'Editor search returns only published, unprotected items');
    check(PCE_Search::search('attachment', '') === [] && PCE_Search::search('nope', 'x') === [] && PCE_Search::search([], 'x') === [], 'Editor search rejects unlisted types');
    PCE_Search::search('gift', 'Gift');
    check($GLOBALS['wp_query_args']['s'] === 'Gift' && $GLOBALS['wp_query_args']['posts_per_page'] === PCE_Search::LIMIT && $GLOBALS['wp_query_args']['has_password'] === false, 'Editor search query is bounded and public');
    $_GET = ['post_type' => 'gift', 'term' => 'Gift'];
    PCE_Search::handle();
    check($GLOBALS['ajax']['out']['success'] && count($GLOBALS['ajax']['out']['data']) === 3, 'Search endpoint returns results');
    $GLOBALS['ajax']['can'] = false;
    PCE_Search::handle();
    check($GLOBALS['ajax']['out'] === ['success' => false, 'status' => 403], 'Search endpoint requires editing capability');
    $GLOBALS['ajax']['can'] = true; $GLOBALS['ajax']['nonce_ok'] = false;
    $blocked = false;
    try { PCE_Search::handle(); } catch (\RuntimeException $e) { $blocked = true; }
    check($blocked, 'Search endpoint requires a valid nonce');
    $GLOBALS['ajax']['nonce_ok'] = true;
    // Phase 3: image/text/pagination controls and hover image.
    foreach (['image_position', 'image_height', 'title_max_lines', 'desc_full', 'fraction_color', 'progress_height', 'progress_track_color', 'progress_fill_color', 'desc_words', 'wp_hover_meta'] as $control) {
        check(isset($widget->controls[$control]), 'Phase 3 control registered: ' . $control);
    }
    check($widget->controls['progress_fill_color']['condition'] === ['pagination_type' => 'progressbar'] && $widget->controls['fraction_color']['condition'] === ['pagination_type' => 'fraction'], 'Pagination style controls depend on the pagination type');
    $hover_item = ['title' => 'Hover', 'image' => ['id' => 42], 'image_hover' => ['id' => 42, 'url' => 'https://example.test/hover.png']];
    $html = render(['items' => [$hover_item]]);
    check(substr_count($html, 'class="pce-v5-hover-image"') <= 1 && strpos($html, 'pce-v5-hover-image') !== false, 'Hover image rendered');
    check(strpos($html, 'aria-hidden="true"') !== false, 'Hover image is decorative');
    check(strpos(render(['items' => [['title' => 'No hover', 'image' => ['id' => 42]]]]), 'pce-v5-hover-image') === false, 'No hover image without a value');
    check(strpos(render(['items' => [['title' => 'Bad', 'image_hover' => ['url' => 'javascript:alert(1)']]]]), 'pce-v5-hover-image') === false, 'Unsafe hover image URL rejected');
    check(strpos(render(['items' => [['title' => 'Bad', 'image_hover' => 'oops']]]), 'pce-v5-hover-image') === false, 'Malformed hover image ignored');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'wp_hover_meta' => 'link_field'])[0]['image_hover']['url'] === 'https://example.test/file.pdf' && PCE_Content::items(['wp_post_type' => 'gift'])[0]['image_hover']['url'] === '', 'WordPress hover image from custom field');
    check(PCE_Content::items(['wp_post_type' => 'gift', 'desc_words' => 5])[2]['desc'] === 'one two three four five' && PCE_Content::items(['wp_post_type' => 'gift', 'desc_words' => 1000])[2]['desc'] === 'one two three four five six seven' && PCE_Content::items(['wp_post_type' => 'gift', 'desc_words' => 1])[2]['desc'] === 'one two three four five', 'Summary length applied and clamped');
    // Phase 4: grid layout.
    foreach (['layout', 'grid_columns', 'grid_gap'] as $control) { check(isset($widget->controls[$control]), 'Grid control registered: ' . $control); }
    check($widget->controls['layout']['default'] === 'carousel' && array_keys($widget->controls['layout']['options']) === ['carousel', 'grid'], 'Carousel stays the default layout');
    check($widget->controls['grid_columns']['tablet_default'] === 2 && $widget->controls['grid_columns']['mobile_default'] === 1 && $widget->controls['grid_columns']['condition'] === ['layout' => 'grid'], 'Responsive grid column defaults');
    $grid_html = render(['items' => [$full_item, $full_item], 'layout' => 'grid']);
    check(strpos($grid_html, 'pce-layout-grid') !== false && strpos($grid_html, 'pce-v5-navigation') === false && strpos($grid_html, 'swiper-pagination') === false && strpos($grid_html, 'aria-roledescription') === false, 'Grid markup has no carousel chrome');
    $carousel_html = render(['items' => [$full_item, $full_item]]);
    check(strpos($carousel_html, 'pce-layout-grid') === false && strpos($carousel_html, 'pce-v5-navigation') !== false && strpos($carousel_html, 'aria-roledescription="carousel"') !== false, 'Carousel markup unchanged by default');
    check(strpos(render(['items' => [$full_item], 'layout' => 'bogus']), 'pce-layout-grid') === false, 'Unknown layout falls back to carousel');
    // 2.4.1: second button styling, hover safe space, image fallback, selected-item titles.
    foreach (['btn_color', 'btn_bg', 'btn_border_color', 'btn_color_hover', 'btn_bg_hover', 'btn_border_color_hover'] as $control) {
        check(strpos(array_key_first($widget->controls[$control]['selectors']), 'not(.pce-v5-btn-secondary)') !== false, 'Primary-only color control: ' . $control);
    }
    foreach (['btn2_color', 'btn2_bg', 'btn2_border_color', 'btn2_color_hover', 'btn2_bg_hover', 'btn2_border_color_hover'] as $control) {
        check(isset($widget->controls[$control]) && strpos(array_key_first($widget->controls[$control]['selectors']), '.pce-v5-btn-secondary') !== false, 'Second button control: ' . $control);
    }
    check(array_key_first($widget->controls['card_hover_translate']['selectors']) === '{{WRAPPER}} .pce-v5-card' && isset($widget->controls['card_hover_translate']['selectors']['{{WRAPPER}} .pce-v5-slider']) && isset($widget->controls['card_hover_scale']['selectors']['{{WRAPPER}} .pce-v5-slider']), 'Hover lift and scale reach the slider for safe space');
    check(strpos(render(['items' => [$full_item]]), 'data-placeholder="https://example.test/placeholder.png"') !== false, 'Wrapper exposes the placeholder for image fallback');
    $titles = PCE_Search::titles('gift', '6,3,1,4,99');
    check(array_column($titles, 'id') === [6, 3, 1, 4, 99] && array_column($titles, 'available') === [true, false, true, false, false] && $titles[1]['title'] === '' && $titles[0]['title'] === 'Gift 6', 'Selected IDs resolve to titles; unpublished, protected and missing are flagged');
    check(PCE_Search::titles('attachment', '1') === [] && PCE_Search::titles('gift', 'x,0') === [], 'Selected-title lookup rejects unlisted types and invalid IDs');
    $_GET = ['post_type' => 'gift', 'ids' => '1,2'];
    PCE_Search::handle();
    check(array_column($GLOBALS['ajax']['out']['data'], 'id') === [1, 2], 'Search endpoint resolves selected IDs');
    $fixture_items = [];
    for ($i = 0; $i < 10; $i++) {
        $fixture_items[] = ['_id' => 'card-' . $i, 'title' => 'محصول ' . ($i + 1), 'category' => 'محصول', 'badge_icon' => ['value' => 'test'], 'btn_icon' => ['value' => 'test'], 'desc' => $i % 2 ? str_repeat('توضیحات محصول ', 20) : 'کوتاه', 'price' => '۱۰۰ تومان', 'link' => ['url' => '#product-' . $i]];
    }
    $fixture = ['items' => $fixture_items, 'test_id' => 'fixture', 'slides_per_group' => 3, 'space_between' => ['size' => 20], 'space_between_tablet' => ['size' => 10], 'space_between_mobile' => ['size' => 4], 'show_arrows' => 'yes', 'show_dots' => 'yes', 'loop' => 'yes', 'autoplay' => 'yes', 'autoplay_pause_on_interaction' => 'yes', 'pause_on_hover' => 'yes'];
    if (!is_dir(__DIR__ . '/.generated')) { mkdir(__DIR__ . '/.generated', 0777, true); }
    file_put_contents(__DIR__ . '/.generated/carousel.html', render($fixture));
    $variants = [];
    foreach (['noButton' => ['show_autoplay_button' => ''], 'noAutoplay' => ['autoplay' => ''], 'unequal' => ['equal_height' => ''],
        'motionOptOut' => ['respect_reduced_motion' => ''], 'autoImage' => ['image_ratio' => 'auto'],
        'minimal' => ['card_preset' => 'minimal'], 'catalog' => ['card_preset' => 'catalog'], 'grid' => ['layout' => 'grid']] as $name => $overrides) {
        $variants[$name] = render(array_merge($fixture, $overrides));
    }
    file_put_contents(__DIR__ . '/.generated/variants.json', json_encode($variants));
    // These are the actual selector templates used by Elementor, not duplicate declarations.
    file_put_contents(__DIR__ . '/.generated/controls.json', json_encode($widget->controls));
    echo "PHP regression checks passed: $checks\n";
}
