<?php
if (!defined('ABSPATH')) {
    exit;
}

/** Generic WordPress content source: any public post type, no WooCommerce required. */
final class PCE_Content {
    const ORDERBY = ['date' => 'date', 'name' => 'title', 'title' => 'title', 'ID' => 'ID', 'modified' => 'modified'];

    /** Public post types a site owner can pick from (attachments excluded). */
    public static function post_type_options() {
        $options = [];
        if (function_exists('get_post_types')) {
            foreach (get_post_types(['public' => true], 'objects') as $slug => $object) {
                if ($slug !== 'attachment') {
                    $options[$slug] = $object->labels->singular_name ?? $slug;
                }
            }
        }
        return $options;
    }

    /** Public taxonomies, labelled with the post types they belong to. */
    public static function taxonomy_options() {
        $options = ['' => __('Any group', 'advanced-carousel-pro')];
        if (function_exists('get_taxonomies')) {
            foreach (get_taxonomies(['public' => true], 'objects') as $slug => $object) {
                $options[$slug] = ($object->labels->name ?? $slug) . ' (' . $slug . ')';
            }
        }
        return $options;
    }

    public static function items(array $settings) {
        if (!function_exists('get_post_types') || !class_exists('WP_Query')) {
            return [];
        }
        $post_type = is_string($settings['wp_post_type'] ?? null) ? $settings['wp_post_type'] : '';
        if (!isset(self::post_type_options()[$post_type])) {
            return [];
        }
        $limit = (int) PCE_Settings::number($settings['wp_limit'] ?? '', 8, 1, 40);
        $orderby = self::ORDERBY[$settings['wp_orderby'] ?? ''] ?? 'date';
        $exclude = PCE_Products::ids($settings['wp_exclude_ids'] ?? '');
        if (($settings['wp_exclude_current'] ?? 'yes') === 'yes' && is_singular($post_type)) {
            $exclude[] = (int) get_queried_object_id();
        }
        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'has_password' => false,
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'order' => ($settings['wp_order'] ?? '') === 'ASC' ? 'ASC' : 'DESC',
            'post__not_in' => $exclude,
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
            'suppress_filters' => false,
        ];
        $taxonomy = is_string($settings['wp_taxonomy'] ?? null) ? $settings['wp_taxonomy'] : '';
        $terms = PCE_Products::ids($settings['wp_term_ids'] ?? '');
        if ($taxonomy !== '' && $terms) {
            if (!taxonomy_exists($taxonomy) || !is_object_in_taxonomy($post_type, $taxonomy)) {
                return [];
            }
            $args['tax_query'] = [['taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $terms]];
        }
        $selected = [];
        if (($settings['wp_query'] ?? 'latest') === 'selected') {
            $selected = PCE_Products::ids($settings['wp_selected_ids'] ?? '', 40);
            if (!$selected) {
                return [];
            }
            $args['post__in'] = $selected;
            $args['orderby'] = 'post__in';
            $args['posts_per_page'] = count($selected);
        }
        $query = new WP_Query($args);
        $button = is_string($settings['wp_button_text'] ?? null) && $settings['wp_button_text'] !== '' ? $settings['wp_button_text'] : __('View', 'advanced-carousel-pro');
        $items = [];
        foreach ((array) $query->posts as $post) {
            $id = (int) (is_object($post) ? $post->ID : $post);
            // Defense in depth: filters/extensions must not expose unpublished or protected content.
            if (get_post_status($id) !== 'publish' || get_post_field('post_password', $id) !== '' || in_array($id, $exclude, true)) {
                continue;
            }
            $image_id = (int) get_post_thumbnail_id($id);
            $badge = '';
            if (($settings['wp_badge'] ?? 'term') === 'term' && $taxonomy !== '' && is_object_in_taxonomy($post_type, $taxonomy)) {
                $names = wp_get_post_terms($id, $taxonomy, ['fields' => 'names']);
                $badge = is_array($names) && $names ? (string) reset($names) : '';
            }
            $items[] = [
                '_id' => 'wp-' . $post_type . '-' . $id,
                'title' => get_the_title($id),
                'category' => $badge,
                'image' => ['id' => $image_id, 'url' => $image_id ? wp_get_attachment_image_url($image_id, 'large') : ''],
                'price' => '',
                'price_html' => '',
                'desc' => wp_trim_words(wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_excerpt', $id))), 30),
                'link' => ['url' => get_permalink($id)],
                'btn_text' => $button,
            ];
            if (count($items) >= $limit) {
                break;
            }
        }
        return $items;
    }
}
