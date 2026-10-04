<?php
if (!defined('ABSPATH')) {
    exit;
}

/** Optional product source. Manual cards never require WooCommerce. */
final class PCE_Products {
    /** Ordering handled through the documented WooCommerce query filter: sort key => numeric meta key. */
    const SORT_META = ['price' => '_price', 'popularity' => 'total_sales', 'rating' => '_wc_average_rating'];

    public static function sort_query($query, $vars) {
        $meta = $vars['pce_sort'] ?? '';
        if (is_string($meta) && in_array($meta, self::SORT_META, true)) {
            $query['meta_key'] = $meta;
            $query['orderby'] = 'meta_value_num';
        }
        return $query;
    }

    public static function ids($value, $limit = 200) {
        $tokens = is_array($value) ? $value : preg_split('/[\s,]+/', is_scalar($value) ? (string) $value : '');
        $ids = [];
        foreach ($tokens as $token) {
            if (is_scalar($token) && ctype_digit((string) $token) && (int) $token > 0) {
                $ids[] = (int) $token;
            }
        }
        return array_slice(array_values(array_unique($ids)), 0, $limit);
    }

    public static function items(array $settings) {
        if (!function_exists('wc_get_products')) {
            return [];
        }
        $limit = (int) PCE_Settings::clamp($settings['product_limit'] ?? '', 8, 1, 40);
        $mode = $settings['product_query'] ?? 'latest';
        $args = [
            'status' => 'publish',
            'type' => ['simple', 'variable', 'grouped', 'external'],
            'visibility' => 'catalog',
            'limit' => $limit,
            'orderby' => in_array($settings['product_orderby'] ?? '', ['date', 'name', 'ID', 'modified'], true) ? $settings['product_orderby'] : 'date',
            'pce_sort' => self::SORT_META[$settings['product_orderby'] ?? ''] ?? '',
            'order' => ($settings['product_order'] ?? '') === 'ASC' ? 'ASC' : 'DESC',
            'return' => 'objects',
            'exclude' => self::ids($settings['exclude_product_ids'] ?? ''),
        ];
        if (($settings['exclude_current_product'] ?? 'yes') === 'yes' && is_singular('product')) {
            $args['exclude'][] = get_queried_object_id();
        }
        if (($settings['hide_out_of_stock'] ?? 'yes') === 'yes') {
            $args['stock_status'] = 'instock';
        }
        if (!empty($settings['product_categories']) && is_string($settings['product_categories'])) {
            $args['category'] = array_filter(array_map('sanitize_title', explode(',', $settings['product_categories'])));
        }
        $selected = [];
        if ($mode === 'selected') {
            $selected = self::ids($settings['selected_product_ids'] ?? '', 40);
            if (!$selected) {
                return [];
            }
            $args['include'] = $selected;
            $args['orderby'] = 'none';
            $args['limit'] = count($selected);
        } elseif ($mode === 'sale') {
            $args['include'] = wc_get_product_ids_on_sale();
            if (!$args['include']) {
                return [];
            }
        } elseif ($mode === 'featured') {
            $args['featured'] = true;
        }
        if ($args['pce_sort'] === '' || $mode === 'selected') {
            unset($args['pce_sort']);
        } else {
            add_filter('woocommerce_product_data_store_cpt_get_products_query', [__CLASS__, 'sort_query'], 10, 2);
        }
        $products = wc_get_products($args);
        remove_filter('woocommerce_product_data_store_cpt_get_products_query', [__CLASS__, 'sort_query'], 10);
        if ($selected) {
            $positions = array_flip($selected);
            usort($products, static function ($a, $b) use ($positions) {
                return ($positions[$a->get_id()] ?? PHP_INT_MAX) <=> ($positions[$b->get_id()] ?? PHP_INT_MAX);
            });
        }
        $items = [];
        foreach ($products as $product) {
            // Defense in depth: query filters/extensions must not expose private cards.
            if ($product->get_status() !== 'publish' || !$product->is_visible() || !in_array($product->get_catalog_visibility(), ['visible', 'catalog'], true)
                || get_post_field('post_password', $product->get_id()) !== '' || in_array($product->get_id(), $args['exclude'], true)) {
                continue;
            }
            if (($settings['hide_out_of_stock'] ?? 'yes') === 'yes' && !$product->is_in_stock()) {
                continue;
            }
            $url = $product->get_permalink();
            $button = __('View Product', 'advanced-carousel-pro');
            if (($settings['product_action'] ?? 'view') === 'purchase' && $product->is_in_stock()) {
                if ($product->is_type('external') || ($product->is_type('simple') && $product->is_purchasable())) {
                    $url = $product->add_to_cart_url();
                    $button = $product->add_to_cart_text();
                } elseif ($product->is_type('variable') && $product->is_purchasable()) {
                    $button = __('Select options', 'advanced-carousel-pro');
                }
            }
            $badge = !$product->is_in_stock() ? __('Out of stock', 'advanced-carousel-pro') : ($product->is_on_sale() ? __('Sale', 'advanced-carousel-pro') : '');
            $image_id = $product->get_image_id();
            $items[] = [
                '_id' => 'product-' . $product->get_id(),
                'title' => $product->get_name(),
                'category' => $badge,
                'image' => ['id' => $image_id, 'url' => $image_id ? wp_get_attachment_image_url($image_id, 'large') : ''],
                'price' => '',
                'price_html' => $product->get_price_html(),
                'desc' => wp_trim_words(wp_strip_all_tags(strip_shortcodes($product->get_short_description())), (int) PCE_Settings::clamp($settings['desc_words'] ?? '', 30, 5, 100)),
                'link' => ['url' => $url],
                'btn_text' => $button,
            ];
            if (count($items) >= $limit) {
                break;
            }
        }
        return $items;
    }
}
