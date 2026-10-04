<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit;
}

// Section and tab boundaries organize the editor without changing stored control IDs.
        $this->start_controls_section('content_section', ['label' => __('Content & Products', 'nexa-slider')]);

        $this->add_control('source', [
            'label' => __('Content Source', 'nexa-slider'),
            'type' => Controls_Manager::SELECT,
            'default' => 'manual',
            'options' => ['manual' => __('Manual items', 'nexa-slider'), 'woocommerce' => __('WooCommerce products', 'nexa-slider'), 'wordpress' => __('WordPress content', 'nexa-slider')],
        ]);

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'nexa-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Product Name', 'nexa-slider'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'category',
            [
                'label' => __('Badge Label', 'nexa-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('New', 'nexa-slider'),
            ]
        );

        $repeater->add_control(
            'badge_icon',
            [
                'label' => __('Badge Icon', 'nexa-slider'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'nexa-slider'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'price',
            [
                'label' => __('Price / Label', 'nexa-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => '$149.00',
            ]
        );

        $repeater->add_control('image_hover', [
            'label' => __('Hover Image', 'nexa-slider'), 'type' => Controls_Manager::MEDIA,
            'description' => __('Optional second image shown when the card is hovered or focused.', 'nexa-slider'),
        ]);

        $repeater->add_control('image_alt', [
            'label' => __('Image Alternative Text', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Leave empty to use the media alternative text or product title.', 'nexa-slider'),
        ]);

        $repeater->add_control(
            'desc',
            [
                'label' => __('Description', 'nexa-slider'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Concise product summary goes here.', 'nexa-slider'),
            ]
        );

        $repeater->add_control(
            'btn_text',
            [
                'label' => __('Button Text', 'nexa-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View Product', 'nexa-slider'),
            ]
        );

        $repeater->add_control(
            'btn_icon',
            [
                'label' => __('Button Icon', 'nexa-slider'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'btn_icon_fa4',
            ]
        );

        $repeater->add_control(
            'btn_icon_position',
            [
                'label' => __('Button Icon Position', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => __('Before Text', 'nexa-slider'),
                    'after' => __('After Text', 'nexa-slider'),
                ],
                'condition' => [
                    'btn_icon[value]!' => '',
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __('Button Link', 'nexa-slider'),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://example.com/product',
            ]
        );

        $repeater->add_control('btn2_text', [
            'label' => __('Second Button Text', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional, for example a PDF or brochure. Shown only when a link is set.', 'nexa-slider'),
        ]);

        $repeater->add_control('btn2_link', [
            'label' => __('Second Button Link', 'nexa-slider'), 'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com/brochure.pdf',
        ]);

        $this->add_control(
            'items',
            [
                'label' => __('Items', 'nexa-slider'),
                'type' => Controls_Manager::REPEATER,
                'condition' => ['source' => 'manual'],
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default' => [
                    [
                        'title' => __('Leather Sneaker', 'nexa-slider'),
                        'price' => '$149.00',
                        'category' => __('Best Seller', 'nexa-slider'),
                        'btn_text' => __('View Product', 'nexa-slider'),
                    ],
                    [
                        'title' => __('Office Backpack', 'nexa-slider'),
                        'price' => '$89.00',
                        'category' => __('Popular', 'nexa-slider'),
                        'btn_text' => __('View Product', 'nexa-slider'),
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $wp_condition = ['source' => 'wordpress'];
        $this->start_controls_section('wordpress_source_settings', ['label' => __('WordPress Content', 'nexa-slider'), 'condition' => $wp_condition]);

        $post_types = PCE_Content::post_type_options();
        $this->add_control('wp_post_type', [
            'label' => __('Content Type', 'nexa-slider'), 'type' => Controls_Manager::SELECT,
            'default' => isset($post_types['post']) ? 'post' : (string) key($post_types), 'options' => $post_types, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_query', [
            'label' => __('Selection', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'latest', 'condition' => $wp_condition,
            'options' => ['latest' => __('All / latest', 'nexa-slider'), 'selected' => __('Selected IDs', 'nexa-slider')],
        ]);
        $this->add_control('wp_selected_ids', [
            'label' => __('Item IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Comma-separated IDs, in display order. Maximum 40 items.', 'nexa-slider'),
            'condition' => ['source' => 'wordpress', 'wp_query' => 'selected'],
        ]);
        $this->add_control('wp_selected_ids_picker', [
            'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="pce-picker" data-target="wp_selected_ids" data-type-control="wp_post_type"></div>',
            'condition' => ['source' => 'wordpress', 'wp_query' => 'selected'], 'content_classes' => 'pce-picker-wrap',
        ]);
        $this->add_control('wp_taxonomy', [
            'label' => __('Group Taxonomy', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => '',
            'options' => PCE_Content::taxonomy_options(), 'condition' => $wp_condition,
            'description' => __('Optional. Must be registered for the selected content type.', 'nexa-slider'),
        ]);
        $this->add_control('wp_term_ids', [
            'label' => __('Group (Term) IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional comma-separated term IDs. Leave empty for all groups.', 'nexa-slider'),
            'condition' => ['source' => 'wordpress', 'wp_taxonomy!' => ''],
        ]);
        $this->add_control('wp_taxonomy_2', [
            'label' => __('Second Group Taxonomy', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => '',
            'options' => PCE_Content::taxonomy_options(), 'condition' => $wp_condition,
            'description' => __('Optional. Items must match both group filters.', 'nexa-slider'),
        ]);
        $this->add_control('wp_term_ids_2', [
            'label' => __('Second Group (Term) IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'condition' => ['source' => 'wordpress', 'wp_taxonomy_2!' => ''],
        ]);
        $this->add_control('wp_limit', [
            'label' => __('Maximum Items', 'nexa-slider'), 'type' => Controls_Manager::NUMBER,
            'default' => 8, 'min' => 1, 'max' => 40, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_orderby', [
            'label' => __('Order By', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'date',
            'options' => ['date' => __('Date', 'nexa-slider'), 'title' => __('Title', 'nexa-slider'), 'modified' => __('Last updated', 'nexa-slider'), 'ID' => __('ID', 'nexa-slider')],
            'condition' => ['source' => 'wordpress', 'wp_query!' => 'selected'],
        ]);
        $this->add_control('wp_order', [
            'label' => __('Order', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'DESC',
            'options' => ['DESC' => __('Descending', 'nexa-slider'), 'ASC' => __('Ascending', 'nexa-slider')],
            'condition' => ['source' => 'wordpress', 'wp_query!' => 'selected'],
        ]);
        $this->add_control('wp_exclude_ids', ['label' => __('Exclude IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition]);
        $this->add_control('wp_exclude_current', ['label' => __('Exclude Current Item', 'nexa-slider'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $wp_condition]);
        $this->add_control('wp_badge', [
            'label' => __('Badge', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'term', 'condition' => $wp_condition,
            'options' => ['term' => __('First group name', 'nexa-slider'), 'none' => __('None', 'nexa-slider')],
        ]);
        $this->add_control('wp_hover_meta', [
            'label' => __('Hover Image Custom Field', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
            'description' => __('Custom field holding an image URL or media ID.', 'nexa-slider'),
        ]);
        $this->add_control('desc_words', [
            'label' => __('Summary Length (words)', 'nexa-slider'), 'type' => Controls_Manager::NUMBER, 'default' => 30, 'min' => 5, 'max' => 100,
            'condition' => ['source!' => 'manual'],
        ]);
        $this->add_control('wp_price_meta', [
            'label' => __('Price / Label Custom Field', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
            'description' => __('Optional custom field name (works with ACF field names). Private fields starting with an underscore are ignored.', 'nexa-slider'),
        ]);
        $this->add_control('wp_btn2_text', [
            'label' => __('Second Button Text', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
        ]);
        $this->add_control('wp_btn2_meta', [
            'label' => __('Second Button Link Field', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $wp_condition,
            'description' => __('Custom field holding a URL or a media file ID, such as a PDF. The button appears only on items that have a value.', 'nexa-slider'),
        ]);
        $this->add_control('wp_button_text', [
            'label' => __('Button Text', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'default' => __('View', 'nexa-slider'), 'condition' => $wp_condition,
        ]);
        $this->end_controls_section();

        $this->start_controls_section('product_source_settings', ['label' => __('Product Selection', 'nexa-slider'), 'condition' => ['source' => 'woocommerce']]);

        $woo_condition = ['source' => 'woocommerce'];
        $this->add_control('product_query', [
            'label' => __('Product Selection', 'nexa-slider'), 'type' => Controls_Manager::SELECT,
            'default' => 'latest', 'condition' => $woo_condition,
            'options' => ['latest' => __('Latest products', 'nexa-slider'), 'selected' => __('Selected product IDs', 'nexa-slider'), 'sale' => __('On sale', 'nexa-slider'), 'featured' => __('Featured', 'nexa-slider')],
        ]);

        $this->add_control('selected_product_ids', [
            'label' => __('Product IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Comma-separated IDs, in display order. Maximum 40 products.', 'nexa-slider'),
            'condition' => ['source' => 'woocommerce', 'product_query' => 'selected'],
        ]);

        $this->add_control('selected_product_ids_picker', [
            'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="pce-picker" data-target="selected_product_ids" data-post-type="product"></div>',
            'condition' => ['source' => 'woocommerce', 'product_query' => 'selected'], 'content_classes' => 'pce-picker-wrap',
        ]);
        $this->add_control('product_categories', [
            'label' => __('Category Slugs', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'description' => __('Optional comma-separated category slugs. Leave empty for all categories.', 'nexa-slider'), 'condition' => $woo_condition,
        ]);

        $this->add_control('product_limit', [
            'label' => __('Maximum Products', 'nexa-slider'), 'type' => Controls_Manager::NUMBER,
            'default' => 8, 'min' => 1, 'max' => 40, 'condition' => $woo_condition,
        ]);
        $this->add_control('hide_out_of_stock', ['label' => __('Hide Out of Stock', 'nexa-slider'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $woo_condition]);

        $this->add_control('product_action', [
            'label' => __('Product Button Action', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'view',
            'options' => ['view' => __('View product', 'nexa-slider'), 'purchase' => __('Purchase / select options', 'nexa-slider')], 'condition' => $woo_condition,
        ]);
        $this->end_controls_section();

        $this->start_controls_section('card_content_settings', ['label' => __('Card Content', 'nexa-slider')]);

        foreach (['image' => __('Show Image', 'nexa-slider'), 'title' => __('Show Title', 'nexa-slider'), 'price' => __('Show Price', 'nexa-slider'), 'badge' => __('Show Badge', 'nexa-slider'), 'description' => __('Show Description', 'nexa-slider'), 'button' => __('Show Button', 'nexa-slider')] as $part => $label) {
            $this->add_control('show_' . $part, ['label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        }
        $this->add_control('card_link', [
            'label' => __('Card Link', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'button',
            'options' => ['button' => __('Button only', 'nexa-slider'), 'title' => __('Button and title', 'nexa-slider'), 'card' => __('Whole card', 'nexa-slider')],
            'description' => __('Uses the item link. The title link covers the card in whole-card mode; other links stay separately clickable.', 'nexa-slider'),
        ]);
        $this->end_controls_section();

        $this->start_controls_section('slider_settings', ['label' => __('Layout', 'nexa-slider')]);

        $this->add_control('layout', [
            'label' => __('Layout', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'carousel',
            'options' => ['carousel' => __('Carousel', 'nexa-slider'), 'grid' => __('Grid (no sliding)', 'nexa-slider')],
            'description' => __('Grid shows every card at once. Motion, autoplay and navigation settings apply to the carousel only.', 'nexa-slider'),
        ]);

        $this->add_responsive_control('grid_columns', [
            'label' => __('Grid Columns', 'nexa-slider'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'step' => 1,
            'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1, 'condition' => ['layout' => 'grid'],
            'selectors' => ['{{WRAPPER}} .pce-v5-slider .swiper-wrapper' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));'],
        ]);

        $this->add_responsive_control('grid_gap', [
            'label' => __('Grid Gap', 'nexa-slider'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 60]],
            'default' => ['size' => 20], 'condition' => ['layout' => 'grid'],
            'selectors' => ['{{WRAPPER}} .pce-v5-slider .swiper-wrapper' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_responsive_control(
            'slides_per_view',
            [
                'frontend_available' => true,
                'label' => __('Visible Slides', 'nexa-slider'),
                'type' => Controls_Manager::NUMBER,
                'placeholder' => __('Auto', 'nexa-slider'),
                'description' => __('Leave empty to use saved values or inherit from a larger screen.', 'nexa-slider'),
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
                'label' => __('Gap', 'nexa-slider'),
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
            'label' => __('Equal Card Heights', 'nexa-slider'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ]);
        $this->end_controls_section();

        $this->start_controls_section('motion_settings', ['label' => __('Motion', 'nexa-slider'), 'condition' => ['layout!' => 'grid']]);

        $this->add_control(
            'slider_effect',
            [
                'label' => __('Transition Effect', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'nexa-slider'),
                    'fade' => __('Fade', 'nexa-slider'),
                    'coverflow' => __('Coverflow', 'nexa-slider'),
                    'cards' => __('Cards', 'nexa-slider'),
                    'creative' => __('Creative', 'nexa-slider'),
                ],
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => __('Transition Speed (ms)', 'nexa-slider'),
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
                'label' => __('Loop', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'allow_touch_move',
            [
                'label' => __('Enable Drag/Swipe', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control('navigation_notice', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => esc_html__('Keep arrows enabled when drag is disabled. Fraction and progress pagination do not provide navigation.', 'nexa-slider'),
            'condition' => ['allow_touch_move!' => 'yes'],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('autoplay_settings', ['label' => __('Autoplay', 'nexa-slider'), 'condition' => ['layout!' => 'grid']]);

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'nexa-slider'),
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
            'label' => __('Show Play/Pause Button', 'nexa-slider'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => ['autoplay' => 'yes'],
        ]);

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __('Pause On Hover', 'nexa-slider'),
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
                'label' => __('Stop Autoplay After Interaction', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section('navigation_settings', ['label' => __('Navigation', 'nexa-slider'), 'condition' => ['layout!' => 'grid']]);

        $this->add_responsive_control(
            'show_arrows',
            [
                'frontend_available' => true,
                'label' => __('Show Arrows', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'show_dots',
            [
                'frontend_available' => true,
                'label' => __('Show Dots', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Pagination Type', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    'bullets' => __('Bullets', 'nexa-slider'),
                    'fraction' => __('Fraction', 'nexa-slider'),
                    'progressbar' => __('Progress Bar', 'nexa-slider'),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section('advanced_slider_settings', ['label' => __('Fine Tuning', 'nexa-slider')]);

        // Tabs only organize the UI; no new mode switch gates existing saved settings.
        $this->start_controls_tabs('fine_tuning_tabs');
        $this->start_controls_tab('fine_tuning_layout', ['label' => __('Layout', 'nexa-slider')]);

        $this->add_responsive_control(
            'slides_per_group',
            [
                'frontend_available' => true,
                'label' => __('Slides Per Group', 'nexa-slider'),
                'description' => __('Fractional views and single-card effects move one card at a time. Insufficient cards use rewind instead of loop.', 'nexa-slider'),
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
                'label' => __('Centered Slides', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'flow_direction',
            [
                'label' => __('Slide Direction', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto (Use Site Direction)', 'nexa-slider'),
                    'ltr' => __('Left To Right', 'nexa-slider'),
                    'rtl' => __('Right To Left', 'nexa-slider'),
                ],
            ]
        );

        $this->add_control(
            'rewind',
            [
                'label' => __('Rewind (When Loop Is Off)', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'dynamic_bullets',
            [
                'label' => __('Dynamic Bullets', 'nexa-slider'),
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
                'label' => __('Dynamic Main Bullets', 'nexa-slider'),
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

        $this->start_controls_tab('fine_tuning_input', ['label' => __('Input', 'nexa-slider')]);

        $this->add_control(
            'drag_threshold',
            [
                'label' => __('Drag Threshold (px)', 'nexa-slider'),
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
                'label' => __('Enable Mousewheel Control', 'nexa-slider'),
                'description' => __('Scroll over the carousel to change slides. Page scrolling resumes at the edges when loop is off.', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'mousewheel_sensitivity',
            [
                'label' => __('Mousewheel Sensitivity', 'nexa-slider'),
                'description' => __('Higher values respond to smaller wheel movements.', 'nexa-slider'),
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
                'label' => __('Release Mousewheel On Edges', 'nexa-slider'),
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
                'label' => __('Enable Keyboard Control', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'respect_reduced_motion',
            [
                'label' => __('Respect Reduced Motion Preference', 'nexa-slider'),
                'description' => __('If enabled, autoplay and heavy motion are reduced for users who prefer reduced motion.', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_reverse',
            [
                'label' => __('Reverse Autoplay Direction', 'nexa-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab('fine_tuning_content', ['label' => __('Content', 'nexa-slider')]);

        $this->add_control('carousel_label', [
            'label' => __('Accessible Carousel Name', 'nexa-slider'), 'type' => Controls_Manager::TEXT,
            'default' => __('Product carousel', 'nexa-slider'),
        ]);

        $this->add_control(
            'title_html_tag',
            [
                'label' => __('Title HTML Tag', 'nexa-slider'),
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
            'label' => __('Image Loading', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'lazy',
            'options' => ['lazy' => __('Lazy', 'nexa-slider'), 'eager' => __('Eager (above the fold)', 'nexa-slider')],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('fine_tuning_products', ['label' => __('Products', 'nexa-slider'), 'condition' => $woo_condition]);

        $this->add_control('product_orderby', [
            'label' => __('Order By', 'nexa-slider'), 'type' => Controls_Manager::SELECT,
            'default' => 'date', 'options' => ['date' => __('Date', 'nexa-slider'), 'name' => __('Name', 'nexa-slider'), 'modified' => __('Last updated', 'nexa-slider'), 'ID' => __('Product ID', 'nexa-slider'), 'price' => __('Price', 'nexa-slider'), 'popularity' => __('Sales', 'nexa-slider'), 'rating' => __('Rating', 'nexa-slider')],
            'condition' => ['source' => 'woocommerce', 'product_query!' => 'selected'],
        ]);

        $this->add_control('product_order', [
            'label' => __('Order', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'DESC',
            'options' => ['DESC' => __('Descending', 'nexa-slider'), 'ASC' => __('Ascending', 'nexa-slider')],
            'condition' => ['source' => 'woocommerce', 'product_query!' => 'selected'],
        ]);

        $this->add_control('exclude_product_ids', [
            'label' => __('Exclude Product IDs', 'nexa-slider'), 'type' => Controls_Manager::TEXT, 'condition' => $woo_condition,
        ]);
        $this->add_control('exclude_current_product', ['label' => __('Exclude Current Product', 'nexa-slider'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => $woo_condition]);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
