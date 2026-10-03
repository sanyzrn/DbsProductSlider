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
    function get_post_meta($id, $key, $single) { return 'Media alt'; }
    function wp_get_attachment_image($id, $size, $icon, $attributes) {
        if ($id === 999) { return ''; }
        return '<img src="https://example.test/' . $size . '.png" width="768" height="768" srcset="https://example.test/small.png 300w, https://example.test/large.png 768w" sizes="(max-width: 768px) 100vw, 768px" alt="' . esc_attr($attributes['alt']) . '" loading="' . $attributes['loading'] . '" decoding="async" />';
    }
    function wp_get_attachment_image_url($id, $size) { return 'https://example.test/product.png'; }
    function wp_kses_post($html) { return strip_tags($html, '<del><ins><span><bdi>'); }
    function wp_strip_all_tags($html) { return strip_tags($html); }
    function strip_shortcodes($text) { return preg_replace('/\[[^]]+\]/', '', $text); }
    function wp_trim_words($text, $count) { return implode(' ', array_slice(explode(' ', $text), 0, $count)); }
    function sanitize_title($value) { return strtolower(trim($value)); }
    function is_singular($type) { return $GLOBALS['current_product'] > 0; }
    function get_queried_object_id() { return $GLOBALS['current_product']; }
    function get_post_field($field, $id) { return $id === 9 ? 'secret' : ''; }
    function wc_get_product_ids_on_sale() { return $GLOBALS['sale_ids']; }
    function wc_get_products($args) { $GLOBALS['query_args'] = $args; return $GLOBALS['products']; }
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
    \Elementor\Plugin::$instance = (object) ['editor' => new class { public function is_edit_mode() { return false; } }];
    require ACP_PLUGIN_PATH . 'includes/class-pce-settings.php';
    require ACP_PLUGIN_PATH . 'includes/class-pce-products.php';
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
        'minimal' => ['card_preset' => 'minimal'], 'catalog' => ['card_preset' => 'catalog']] as $name => $overrides) {
        $variants[$name] = render(array_merge($fixture, $overrides));
    }
    file_put_contents(__DIR__ . '/.generated/variants.json', json_encode($variants));
    // These are the actual selector templates used by Elementor, not duplicate declarations.
    file_put_contents(__DIR__ . '/.generated/controls.json', json_encode($widget->controls));
    echo "PHP regression checks passed: $checks\n";
}
