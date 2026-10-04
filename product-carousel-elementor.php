<?php
/**
 * Plugin Name: Product Carousel Elementor
 * Plugin URI: https://dbsgraphic.ir/
 * Description: Manual and optional WooCommerce product carousels for Elementor with responsive controls, Persian UI and accessible playback.
 * Version: 2.3.2
 * Author: Saeed Zarrini
 * Author URI: https://dbsgraphic.ir/
 * Text Domain: advanced-carousel-pro
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

final class ACP_Plugin {
    private const MINIMUM_ELEMENTOR_VERSION = '3.15.0';
    private const MINIMUM_PHP_VERSION = '7.4';
    private const VERSION = '2.3.2';

    public function __construct() {
        $this->define_constants();

        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('plugins_loaded', [$this, 'init']);
    }

    private function define_constants() {
        define('ACP_PLUGIN_FILE', __FILE__);
        define('ACP_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('ACP_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('ACP_PLUGIN_VERSION', self::VERSION);
    }

    public function load_textdomain() {
        load_plugin_textdomain('advanced-carousel-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init() {
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        if (!defined('ELEMENTOR_VERSION') || version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        require_once ACP_PLUGIN_PATH . 'includes/class-pce-settings.php';
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-products.php';
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-content.php';
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-search.php';
        PCE_Search::register();

        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'register_assets']);
        add_action('elementor/frontend/after_register_styles', [$this, 'register_assets']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    public function register_widgets($widgets_manager) {
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-settings.php';
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-products.php';
        require_once ACP_PLUGIN_PATH . 'includes/class-pce-content.php';
        require_once ACP_PLUGIN_PATH . 'widgets/carousel.php';
        $widgets_manager->register(new \PCE_Carousel_V5());
    }

    public function register_assets() {
        // A pinned, namespaced engine keeps Elementor's own Swiper untouched.
        wp_register_style('pce-swiper', ACP_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.css', [], '11.2.10');
        wp_register_script('pce-swiper', ACP_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.js', [], '11.2.10', true);

        wp_register_style(
            'pce-v5-style',
            ACP_PLUGIN_URL . 'assets/css/style.css',
            ['pce-swiper'],
            ACP_PLUGIN_VERSION
        );

        wp_register_script(
            'pce-v5-script',
            ACP_PLUGIN_URL . 'assets/js/script.js',
            ['jquery', 'pce-swiper', 'elementor-frontend'],
            ACP_PLUGIN_VERSION,
            true
        );
    }

    public function admin_notice_missing_main_plugin() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            esc_html__('Product Carousel Elementor requires Elementor to be installed and active.', 'advanced-carousel-pro')
        );
    }

    public function admin_notice_minimum_elementor_version() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            esc_html(sprintf(
                /* translators: %s: minimum Elementor version */
                __('Product Carousel Elementor requires Elementor version %s or greater.', 'advanced-carousel-pro'),
                self::MINIMUM_ELEMENTOR_VERSION
            ))
        );
    }

    public function admin_notice_minimum_php_version() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            esc_html(sprintf(
                /* translators: %s: minimum PHP version */
                __('Product Carousel Elementor requires PHP version %s or greater.', 'advanced-carousel-pro'),
                self::MINIMUM_PHP_VERSION
            ))
        );
    }
}

new ACP_Plugin();
