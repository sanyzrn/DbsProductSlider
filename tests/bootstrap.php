<?php
define('ABSPATH', __DIR__);
$mode = $argv[1] ?? 'ready';
define('ELEMENTOR_VERSION', $mode === 'old' ? '3.14.0' : '3.15.0');
$actions = [];
$scripts = ['swiper' => ['original-elementor.js']];
$styles = ['swiper' => ['original-elementor.css']];
function add_action($hook, $callback) { $GLOBALS['actions'][$hook][] = $callback; }
function plugin_dir_path($file) { return dirname($file) . '/'; }
function plugin_dir_url($file) { return 'https://example.test/plugins/nexa-slider/'; }
function did_action($hook) { return $GLOBALS['mode'] === 'missing' ? 0 : 1; }
function current_user_can($capability) { return false; }
function wp_register_script($handle, $url, $deps = [], $version = '', $footer = false) { $GLOBALS['scripts'][$handle] = [$url, $deps, $version, $footer]; }
function wp_register_style($handle, $url, $deps = [], $version = '') { $GLOBALS['styles'][$handle] = [$url, $deps, $version]; }
if ($mode === 'duplicate') { define('ACP_PLUGIN_FILE', '/old/product-carousel-elementor.php'); }
function esc_html__($text, $domain = '') { return $text; }
require dirname(__DIR__) . '/nexa-slider.php';
if ($mode === 'duplicate') {
    if (isset($actions['plugins_loaded']) || !isset($actions['admin_notices'])) { throw new RuntimeException('Duplicate guard must stop loading and warn'); }
    echo "Bootstrap check passed: duplicate\n";
    return;
}
$plugin = null;
foreach ($actions['plugins_loaded'] as $callback) {
    if ($callback[1] === 'init') { $plugin = $callback[0]; $plugin->init(); }
}
if ($mode === 'ready') {
    if (!isset($actions['elementor/widgets/register'], $actions['elementor/frontend/after_register_scripts'])) { throw new RuntimeException('Missing registration hooks'); }
    $plugin->register_assets();
    if ($scripts['swiper'] !== ['original-elementor.js'] || $styles['swiper'] !== ['original-elementor.css']) { throw new RuntimeException('Elementor handles overwritten'); }
    if ($scripts['pce-v5-script'][1] !== ['jquery', 'pce-swiper', 'elementor-frontend']) { throw new RuntimeException('Incomplete dependency order'); }
    if ($scripts['pce-swiper'][2] !== '11.2.10' || strpos($scripts['pce-swiper'][0], 'assets/vendor/') === false) { throw new RuntimeException('Nonlocal/unpinned engine'); }
} else {
    if (!isset($actions['admin_notices']) || isset($actions['elementor/widgets/register'])) { throw new RuntimeException('Dependency guard failed'); }
    ob_start();
    foreach ($actions['admin_notices'] as $callback) { call_user_func($callback); }
    if (ob_get_clean() !== '') { throw new RuntimeException('Notice exposed without activate_plugins capability'); }
}
echo "Bootstrap check passed: $mode\n";
