<?php
if (!defined('ABSPATH')) {
    exit;
}

/** Editor-only content search that fills the "selected IDs" fields. Read-only; never changes content. */
final class PCE_Search {
    const ACTION = 'pce_search_content';
    const LIMIT = 20;

    public static function register() {
        add_action('wp_ajax_' . self::ACTION, [__CLASS__, 'handle']);
        add_action('elementor/editor/after_enqueue_scripts', [__CLASS__, 'enqueue']);
    }

    public static function enqueue() {
        wp_enqueue_script('pce-editor', ACP_PLUGIN_URL . 'assets/js/editor.js', [], ACP_PLUGIN_VERSION, true);
        wp_localize_script('pce-editor', 'pceEditor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'action' => self::ACTION,
            'nonce' => wp_create_nonce(self::ACTION),
            'i18n' => [
                'placeholder' => __('Search by title or SKU…', 'nexa-slider'),
                'empty' => __('No published items found.', 'nexa-slider'),
                'error' => __('Search failed. Try again.', 'nexa-slider'),
                'added' => __('Added', 'nexa-slider'),
                'remove' => __('Remove', 'nexa-slider'),
                'unavailable' => __('unavailable', 'nexa-slider'),
            ],
        ]);
    }

    /** Published, public, non-password items of a listed public content type. */
    public static function search($post_type, $term) {
        if (!is_string($post_type) || !isset(PCE_Content::post_type_options()[$post_type])) {
            return [];
        }
        $term = is_string($term) ? trim($term) : '';
        $base = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'has_password' => false,
            'posts_per_page' => self::LIMIT,
            'no_found_rows' => true,
            'fields' => 'ids',
            'ignore_sticky_posts' => true,
        ];
        $queries = [$term === '' ? $base : $base + ['s' => $term]];
        if ($term !== '' && $post_type === 'product') {
            $queries[] = $base + ['meta_query' => [['key' => '_sku', 'value' => $term, 'compare' => 'LIKE']]];
        }
        $ids = [];
        foreach ($queries as $args) {
            $query = new WP_Query($args);
            foreach ((array) $query->posts as $id) {
                $ids[(int) $id] = true;
            }
        }
        $rows = [];
        foreach (array_slice(array_keys($ids), 0, self::LIMIT) as $id) {
            if (get_post_status($id) !== 'publish' || get_post_field('post_password', $id) !== '') {
                continue;
            }
            $rows[] = ['id' => $id, 'title' => wp_strip_all_tags(get_the_title($id))];
        }
        return $rows;
    }

    /** Titles for already selected IDs, in the given order; unavailable IDs are reported so they can be removed. */
    public static function titles($post_type, $ids) {
        if (!is_string($post_type) || !isset(PCE_Content::post_type_options()[$post_type])) {
            return [];
        }
        $ids = PCE_Products::ids($ids, 40);
        if (!$ids) {
            return [];
        }
        $query = new WP_Query([
            'post_type' => $post_type, 'post_status' => 'publish', 'has_password' => false, 'post__in' => $ids, 'orderby' => 'post__in',
            'posts_per_page' => count($ids), 'no_found_rows' => true, 'fields' => 'ids', 'ignore_sticky_posts' => true,
        ]);
        $found = array_map('intval', (array) $query->posts);
        $rows = [];
        foreach ($ids as $id) {
            $available = in_array($id, $found, true) && get_post_status($id) === 'publish' && get_post_field('post_password', $id) === '';
            $rows[] = ['id' => $id, 'title' => $available ? wp_strip_all_tags(get_the_title($id)) : '', 'available' => $available];
        }
        return $rows;
    }

    public static function handle() {
        check_ajax_referer(self::ACTION, 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(null, 403);
            return;
        }
        $post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : '';
        $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
        if (isset($_GET['ids'])) {
            wp_send_json_success(self::titles($post_type, sanitize_text_field(wp_unslash($_GET['ids']))));
            return;
        }
        wp_send_json_success(self::search($post_type, $term));
    }
}
