<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit;
}

// Section and tab boundaries organize the editor without changing stored control IDs.
        $this->start_controls_section('content_section', ['label' => __('Content & Products', 'advanced-carousel-pro')]);

        $this->add_control('source', [
            'label' => __('Content Source', 'advanced-carousel-pro'),
            'type' => Controls_Manager::SELECT,
            'default' => 'manual',
            'options' => ['manual' => __('Manual items', 'advanced-carousel-pro'), 'woocommerce' => __('WooCommerce products', 'advanced-carousel-pro'), 'wordpress' => __('WordPress content', 'advanced-carousel-pro')],
        ]);

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'advanced-carousel-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Product Name', 'advanced-carousel-pro'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'category',
            [
                'label' => __('Badge Label', 'advanced-carousel-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('New', 'advanced-carousel-pro'),
            ]
        );

        $repeater->add_control(
            'badge_icon',
            [
                'label' => __('Badge Icon', 'advanced-carousel-pro'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'advanced-carousel-pro'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'price',
            [
                'label' => __('Price / Label', 'advanced-carousel-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => '$149.00',
            ]
        );

        $repeater->add_control('image_alt', [
            'label' => __('Image Alternative Text', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Leave empty to use the media alternative text or product title.', 'advanced-carousel-pro'),
        ]);

        $repeater->add_control(
            'desc',
            [
                'label' => __('Description', 'advanced-carousel-pro'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Concise product summary goes here.', 'advanced-carousel-pro'),
            ]
        );

        $repeater->add_control(
            'btn_text',
            [
                'label' => __('Button Text', 'advanced-carousel-pro'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View Product', 'advanced-carousel-pro'),
            ]
        );

        $repeater->add_control(
            'btn_icon',
            [
                'label' => __('Button Icon', 'advanced-carousel-pro'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'btn_icon_fa4',
            ]
        );

        $repeater->add_control(
            'btn_icon_position',
            [
                'label' => __('Button Icon Position', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => __('Before Text', 'advanced-carousel-pro'),
                    'after' => __('After Text', 'advanced-carousel-pro'),
                ],
                'condition' => [
                    'btn_icon[value]!' => '',
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __('Button Link', 'advanced-carousel-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://example.com/product',
            ]
        );

        $repeater->add_control('btn2_text', [
            'label' => __('Second Button Text', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional, for example a PDF or brochure. Shown only when a link is set.', 'advanced-carousel-pro'),
        ]);

        $repeater->add_control('btn2_link', [
            'label' => __('Second Button Link', 'advanced-carousel-pro'), 'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com/brochure.pdf',
        ]);

        $this->add_control(
            'items',
            [
                'label' => __('Items', 'advanced-carousel-pro'),
                'type' => Controls_Manager::REPEATER,
                'condition' => ['source' => 'manual'],
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default' => [
                    [
                        'title' => __('Leather Sneaker', 'advanced-carousel-pro'),
                        'price' => '$149.00',
                        'category' => __('Best Seller', 'advanced-carousel-pro'),
                        'btn_text' => __('View Product', 'advanced-carousel-pro'),
                    ],
                    [
                        'title' => __('Office Backpack', 'advanced-carousel-pro'),
                        'price' => '$89.00',
                        'category' => __('Popular', 'advanced-carousel-pro'),
                        'btn_text' => __('View Product', 'advanced-carousel-pro'),
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $wp_condition = ['source' => 'wordpress'];
        $this->start_controls_section('wordpress_source_settings', ['label' => __('WordPress Content', 'advanced-carousel-pro'), 'condition' => $wp_condition]);

        $post_types = PCE_Content::post_type_options();
        $this->add_control('wp_post_type', [
            'label' => __('Content Type', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT,
            'default' => isset($post_types['post']) ? 'post' : (string) key($post_types), 'options' => $post_types, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_query', [
            'label' => __('Selection', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'latest', 'condition' => $wp_condition,
            'options' => ['latest' => __('All / latest', 'advanced-carousel-pro'), 'selected' => __('Selected IDs', 'advanced-carousel-pro')],
        ]);
        $this->add_control('wp_selected_ids', [
            'label' => __('Item IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Comma-separated IDs, in display order. Maximum 40 items.', 'advanced-carousel-pro'),
            'condition' => ['source' => 'wordpress', 'wp_query' => 'selected'],
        ]);
        $this->add_control('wp_selected_ids_picker', [
            'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="pce-picker" data-target="wp_selected_ids" data-type-control="wp_post_type"></div>',
            'condition' => ['source' => 'wordpress', 'wp_query' => 'selected'], 'content_classes' => 'pce-picker-wrap',
        ]);
        $this->add_control('wp_taxonomy', [
            'label' => __('Group Taxonomy', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => '',
            'options' => PCE_Content::taxonomy_options(), 'condition' => $wp_condition,
            'description' => __('Optional. Must be registered for the selected content type.', 'advanced-carousel-pro'),
        ]);
        $this->add_control('wp_term_ids', [
            'label' => __('Group (Term) IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional comma-separated term IDs. Leave empty for all groups.', 'advanced-carousel-pro'),
            'condition' => ['source' => 'wordpress', 'wp_taxonomy!' => ''],
        ]);
        $this->add_control('wp_taxonomy_2', [
            'label' => __('Second Group Taxonomy', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => '',
            'options' => PCE_Content::taxonomy_options(), 'condition' => $wp_condition,
            'description' => __('Optional. Items must match both group filters.', 'advanced-carousel-pro'),
        ]);
        $this->add_control('wp_term_ids_2', [
            'label' => __('Second Group (Term) IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'condition' => ['source' => 'wordpress', 'wp_taxonomy_2!' => ''],
        ]);
        $this->add_control('wp_limit', [
            'label' => __('Maximum Items', 'advanced-carousel-pro'), 'type' => Controls_Manager::NUMBER,
            'default' => 8, 'min' => 1, 'max' => 40, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_orderby', [
            'label' => __('Order By', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'date',
            'options' => ['date' => __('Date', 'advanced-carousel-pro'), 'title' => __('Title', 'advanced-carousel-pro'), 'modified' => __('Last updated', 'advanced-carousel-pro'), 'ID' => __('ID', 'advanced-carousel-pro')],
            'condition' => ['source' => 'wordpress', 'wp_query!' => 'selected'],
        ]);
        $this->add_control('wp_order', [
            'label' => __('Order', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'DESC',
            'options' => ['DESC' => __('Descending', 'advanced-carousel-pro'), 'ASC' => __('Ascending', 'advanced-carousel-pro')],
            'condition' => ['source' => 'wordpress', 'wp_query!' => 'selected'],
        ]);
        $this->add_control('wp_exclude_ids', ['label' => __('Exclude IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition]);
        $this->add_control('wp_exclude_current', ['label' => __('Exclude Current Item', 'advanced-carousel-pro'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $wp_condition]);
        $this->add_control('wp_badge', [
            'label' => __('Badge', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'term', 'condition' => $wp_condition,
            'options' => ['term' => __('First group name', 'advanced-carousel-pro'), 'none' => __('None', 'advanced-carousel-pro')],
        ]);
        $this->add_control('wp_price_meta', [
            'label' => __('Price / Label Custom Field', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
            'description' => __('Optional custom field name (works with ACF field names). Private fields starting with an underscore are ignored.', 'advanced-carousel-pro'),
        ]);
        $this->add_control('wp_btn2_text', [
            'label' => __('Second Button Text', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_btn2_meta', [
            'label' => __('Second Button Link Field', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
            'description' => __('Custom field holding a URL or a media file ID, such as a PDF. The button appears only on items that have a value.', 'advanced-carousel-pro'),
        ]);
        $this->add_control('wp_button_text', [
            'label' => __('Button Text', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'default' => __('View', 'advanced-carousel-pro'), 'condition' => $wp_condition,
        ]);
        $this->end_controls_section();

        $this->start_controls_section('product_source_settings', ['label' => __('Product Selection', 'advanced-carousel-pro'), 'condition' => ['source' => 'woocommerce']]);

        $woo_condition = ['source' => 'woocommerce'];
        $this->add_control('product_query', [
            'label' => __('Product Selection', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT,
            'default' => 'latest', 'condition' => $woo_condition,
            'options' => ['latest' => __('Latest products', 'advanced-carousel-pro'), 'selected' => __('Selected product IDs', 'advanced-carousel-pro'), 'sale' => __('On sale', 'advanced-carousel-pro'), 'featured' => __('Featured', 'advanced-carousel-pro')],
        ]);

        $this->add_control('selected_product_ids', [
            'label' => __('Product IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Comma-separated IDs, in display order. Maximum 40 products.', 'advanced-carousel-pro'),
            'condition' => ['source' => 'woocommerce', 'product_query' => 'selected'],
        ]);

        $this->add_control('selected_product_ids_picker', [
            'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="pce-picker" data-target="selected_product_ids" data-post-type="product"></div>',
            'condition' => ['source' => 'woocommerce', 'product_query' => 'selected'], 'content_classes' => 'pce-picker-wrap',
        ]);
        $this->add_control('product_categories', [
            'label' => __('Category Slugs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional comma-separated category slugs. Leave empty for all categories.', 'advanced-carousel-pro'), 'condition' => $woo_condition,
        ]);

        $this->add_control('product_limit', [
            'label' => __('Maximum Products', 'advanced-carousel-pro'), 'type' => Controls_Manager::NUMBER,
            'default' => 8, 'min' => 1, 'max' => 40, 'condition' => $woo_condition,
        ]);
        $this->add_control('hide_out_of_stock', ['label' => __('Hide Out of Stock', 'advanced-carousel-pro'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $woo_condition]);

        $this->add_control('product_action', [
            'label' => __('Product Button Action', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'view',
            'options' => ['view' => __('View product', 'advanced-carousel-pro'), 'purchase' => __('Purchase / select options', 'advanced-carousel-pro')], 'condition' => $woo_condition,
        ]);
        $this->end_controls_section();

        $this->start_controls_section('card_content_settings', ['label' => __('Card Content', 'advanced-carousel-pro')]);

        foreach (['image' => __('Show Image', 'advanced-carousel-pro'), 'title' => __('Show Title', 'advanced-carousel-pro'), 'price' => __('Show Price', 'advanced-carousel-pro'), 'badge' => __('Show Badge', 'advanced-carousel-pro'), 'description' => __('Show Description', 'advanced-carousel-pro'), 'button' => __('Show Button', 'advanced-carousel-pro')] as $part => $label) {
            $this->add_control('show_' . $part, ['label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        }
        $this->add_control('card_link', [
            'label' => __('Card Link', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'button',
            'options' => ['button' => __('Button only', 'advanced-carousel-pro'), 'title' => __('Button and title', 'advanced-carousel-pro'), 'card' => __('Whole card', 'advanced-carousel-pro')],
            'description' => __('Uses the item link. The title link covers the card in whole-card mode; other links stay separately clickable.', 'advanced-carousel-pro'),
        ]);
        $this->end_controls_section();

        $this->start_controls_section('slider_settings', ['label' => __('Layout', 'advanced-carousel-pro')]);

        $this->add_responsive_control(
            'slides_per_view',
            [
                'frontend_available' => true,
                'label' => __('Visible Slides', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => __('Auto', 'advanced-carousel-pro'),
                'description' => __('Leave empty to use saved values or inherit from a larger screen.', 'advanced-carousel-pro'),
                'min' => 1,
                'max' => 6,
                'step' => 0.05,
            ]
        );

        // Retain saved values from 2.1.x without showing duplicate device controls.
        foreach (['slides_desktop', 'slides_tablet', 'slides_mobile'] as $legacy_control) {
            $this->add_control($legacy_control, ['type' => Controls_Manager::HIDDEN]);
        }

        $this->add_responsive_control(
            'space_between',
            [
                'frontend_available' => true,
                'label' => __('Gap', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
            ]
        );

        $this->add_control('equal_height', [
            'label' => __('Equal Card Heights', 'advanced-carousel-pro'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ]);
        $this->end_controls_section();

        $this->start_controls_section('motion_settings', ['label' => __('Motion', 'advanced-carousel-pro')]);

        $this->add_control(
            'slider_effect',
            [
                'label' => __('Transition Effect', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'advanced-carousel-pro'),
                    'fade' => __('Fade', 'advanced-carousel-pro'),
                    'coverflow' => __('Coverflow', 'advanced-carousel-pro'),
                    'cards' => __('Cards', 'advanced-carousel-pro'),
                    'creative' => __('Creative', 'advanced-carousel-pro'),
                ],
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => __('Transition Speed (ms)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 550,
                'min' => 100,
                'max' => 3000,
                'step' => 50,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __('Loop', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'allow_touch_move',
            [
                'label' => __('Enable Drag/Swipe', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control('navigation_notice', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => esc_html__('Keep arrows enabled when drag is disabled. Fraction and progress pagination do not provide navigation.', 'advanced-carousel-pro'),
            'condition' => ['allow_touch_move!' => 'yes'],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('autoplay_settings', ['label' => __('Autoplay', 'advanced-carousel-pro')]);

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3500,
                'min' => 1000,
                'max' => 15000,
                'step' => 100,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control('show_autoplay_button', [
            'label' => __('Show Play/Pause Button', 'advanced-carousel-pro'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => ['autoplay' => 'yes'],
        ]);

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __('Pause On Hover', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'autoplay_pause_on_interaction',
            [
                'label' => __('Stop Autoplay After Interaction', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section('navigation_settings', ['label' => __('Navigation', 'advanced-carousel-pro')]);

        $this->add_responsive_control(
            'show_arrows',
            [
                'frontend_available' => true,
                'label' => __('Show Arrows', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'show_dots',
            [
                'frontend_available' => true,
                'label' => __('Show Dots', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Pagination Type', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    'bullets' => __('Bullets', 'advanced-carousel-pro'),
                    'fraction' => __('Fraction', 'advanced-carousel-pro'),
                    'progressbar' => __('Progress Bar', 'advanced-carousel-pro'),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section('advanced_slider_settings', ['label' => __('Fine Tuning', 'advanced-carousel-pro')]);

        // Tabs only organize the UI; no new mode switch gates existing saved settings.
        $this->start_controls_tabs('fine_tuning_tabs');
        $this->start_controls_tab('fine_tuning_layout', ['label' => __('Layout', 'advanced-carousel-pro')]);

        $this->add_responsive_control(
            'slides_per_group',
            [
                'frontend_available' => true,
                'label' => __('Slides Per Group', 'advanced-carousel-pro'),
                'description' => __('Fractional views and single-card effects move one card at a time. Insufficient cards use rewind instead of loop.', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 6,
                'step' => 1,
            ]
        );

        $this->add_control(
            'centered_slides',
            [
                'label' => __('Centered Slides', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'flow_direction',
            [
                'label' => __('Slide Direction', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto (Use Site Direction)', 'advanced-carousel-pro'),
                    'ltr' => __('Left To Right', 'advanced-carousel-pro'),
                    'rtl' => __('Right To Left', 'advanced-carousel-pro'),
                ],
            ]
        );

        $this->add_control(
            'rewind',
            [
                'label' => __('Rewind (When Loop Is Off)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'dynamic_bullets',
            [
                'label' => __('Dynamic Bullets', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'pagination_type' => 'bullets',
                ],
            ]
        );

        $this->add_control(
            'dynamic_main_bullets',
            [
                'label' => __('Dynamic Main Bullets', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'condition' => [
                    'pagination_type' => 'bullets',
                    'dynamic_bullets' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab('fine_tuning_input', ['label' => __('Input', 'advanced-carousel-pro')]);

        $this->add_control(
            'drag_threshold',
            [
                'label' => __('Drag Threshold (px)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 8,
                'min' => 0,
                'max' => 60,
                'step' => 1,
                'condition' => [
                    'allow_touch_move' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'mousewheel_control',
            [
                'label' => __('Enable Mousewheel Control', 'advanced-carousel-pro'),
                'description' => __('Scroll over the carousel to change slides. Page scrolling resumes at the edges when loop is off.', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'mousewheel_sensitivity',
            [
                'label' => __('Mousewheel Sensitivity', 'advanced-carousel-pro'),
                'description' => __('Higher values respond to smaller wheel movements.', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0.1,
                'max' => 5,
                'step' => 0.1,
                'condition' => [
                    'mousewheel_control' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'mousewheel_release_on_edges',
            [
                'label' => __('Release Mousewheel On Edges', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'mousewheel_control' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'keyboard_control',
            [
                'label' => __('Enable Keyboard Control', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'respect_reduced_motion',
            [
                'label' => __('Respect Reduced Motion Preference', 'advanced-carousel-pro'),
                'description' => __('If enabled, autoplay and heavy motion are reduced for users who prefer reduced motion.', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_reverse',
            [
                'label' => __('Reverse Autoplay Direction', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab('fine_tuning_content', ['label' => __('Content', 'advanced-carousel-pro')]);

        $this->add_control('carousel_label', [
            'label' => __('Accessible Carousel Name', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT,
            'default' => __('Product carousel', 'advanced-carousel-pro'),
        ]);

        $this->add_control(
            'title_html_tag',
            [
                'label' => __('Title HTML Tag', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'div' => 'DIV',
                ],
            ]
        );

        $this->add_control('image_loading', [
            'label' => __('Image Loading', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'lazy',
            'options' => ['lazy' => __('Lazy', 'advanced-carousel-pro'), 'eager' => __('Eager (above the fold)', 'advanced-carousel-pro')],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('fine_tuning_products', ['label' => __('Products', 'advanced-carousel-pro'), 'condition' => $woo_condition]);

        $this->add_control('product_orderby', [
            'label' => __('Order By', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT,
            'default' => 'date', 'options' => ['date' => __('Date', 'advanced-carousel-pro'), 'name' => __('Name', 'advanced-carousel-pro'), 'modified' => __('Last updated', 'advanced-carousel-pro'), 'ID' => __('Product ID', 'advanced-carousel-pro'), 'price' => __('Price', 'advanced-carousel-pro'), 'popularity' => __('Sales', 'advanced-carousel-pro'), 'rating' => __('Rating', 'advanced-carousel-pro')],
            'condition' => ['source' => 'woocommerce', 'product_query!' => 'selected'],
        ]);

        $this->add_control('product_order', [
            'label' => __('Order', 'advanced-carousel-pro'), 'type' => Controls_Manager::SELECT, 'default' => 'DESC',
            'options' => ['DESC' => __('Descending', 'advanced-carousel-pro'), 'ASC' => __('Ascending', 'advanced-carousel-pro')],
            'condition' => ['source' => 'woocommerce', 'product_query!' => 'selected'],
        ]);

        $this->add_control('exclude_product_ids', [
            'label' => __('Exclude Product IDs', 'advanced-carousel-pro'), 'type' => Controls_Manager::TEXT, 'condition' => $woo_condition,
        ]);
        $this->add_control('exclude_current_product', ['label' => __('Exclude Current Product', 'advanced-carousel-pro'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $woo_condition]);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
