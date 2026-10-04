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

    /** Custom field names must be plain and public; protected/underscore keys are never read. */
    private static function meta_key($value) {
        $key = is_string($value) ? trim($value) : '';
        if ($key === '' || !preg_match('/^[A-Za-z0-9][A-Za-z0-9_\-]{0,63}$/', $key) || (function_exists('is_protected_meta') && is_protected_meta($key, 'post'))) {
            return '';
        }
        return $key;
    }

    private static function meta_text($id, $key) {
        $value = $key === '' ? '' : get_post_meta($id, $key, true);
        return is_scalar($value) ? trim(wp_strip_all_tags((string) $value)) : '';
    }

    /** A URL, or a media ID resolved to its file URL; anything else is dropped. */
    private static function meta_url($id, $key) {
        $value = self::meta_text($id, $key);
        if ($value !== '' && ctype_digit($value)) {
            $value = (string) wp_get_attachment_url((int) $value);
        }
        return $value !== '' && preg_match('#^https?://#i', $value) ? esc_url_raw($value) : '';
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
        $tax_query = [];
        foreach ([['wp_taxonomy', 'wp_term_ids'], ['wp_taxonomy_2', 'wp_term_ids_2']] as [$tax_key, $terms_key]) {
            $slug = is_string($settings[$tax_key] ?? null) ? $settings[$tax_key] : '';
            $terms = PCE_Products::ids($settings[$terms_key] ?? '');
            if ($slug === '' || !$terms) {
                continue;
            }
            if (!taxonomy_exists($slug) || !is_object_in_taxonomy($post_type, $slug)) {
                return [];
            }
            $tax_query[] = ['taxonomy' => $slug, 'field' => 'term_id', 'terms' => $terms];
        }
        if ($tax_query) {
            $args['tax_query'] = count($tax_query) > 1 ? array_merge(['relation' => 'AND'], $tax_query) : $tax_query;
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
        $price_key = self::meta_key($settings['wp_price_meta'] ?? '');
        $hover_key = self::meta_key($settings['wp_hover_meta'] ?? '');
        $words = (int) PCE_Settings::number($settings['desc_words'] ?? '', 30, 5, 100);
        $btn2_key = self::meta_key($settings['wp_btn2_meta'] ?? '');
        $btn2_text = is_string($settings['wp_btn2_text'] ?? null) ? $settings['wp_btn2_text'] : '';
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
                'image_hover' => ['url' => $hover_key !== '' ? self::meta_url($id, $hover_key) : ''],
                'price' => self::meta_text($id, $price_key),
                'price_html' => '',
                'desc' => wp_trim_words(wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_excerpt', $id))), $words),
                'link' => ['url' => get_permalink($id)],
                'btn_text' => $button,
                'btn2_text' => $btn2_text,
                'btn2_link' => ['url' => $btn2_text !== '' ? self::meta_url($id, $btn2_key) : ''],
            ];
            if (count($items) >= $limit) {
                break;
            }
        }
        return $items;
    }
}
