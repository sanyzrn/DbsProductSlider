<?php

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

class PCE_Carousel_V5 extends Widget_Base {

    public function get_name() {
        return 'pce_carousel_v5';
    }

    public function get_title() {
        return __('Product Carousel Pro', 'advanced-carousel-pro');
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['carousel', 'product', 'slider', 'elementor'];
    }

    public function get_script_depends() {
        return ['swiper', 'pce-v5-script'];
    }

    public function get_style_depends() {
        return ['swiper', 'pce-v5-style'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Items', 'advanced-carousel-pro'),
            ]
        );

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

        $this->add_control(
            'items',
            [
                'label' => __('Items', 'advanced-carousel-pro'),
                'type' => Controls_Manager::REPEATER,
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

        $this->add_control(
            'desc_max_lines',
            [
                'label' => __('Description Max Lines', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 8,
                'step' => 1,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-desc' => '-webkit-line-clamp: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'slider_settings',
            [
                'label' => __('Slider Settings', 'advanced-carousel-pro'),
            ]
        );

        $this->add_control(
            'slides_desktop',
            [
                'label' => __('Desktop Slides', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 6,
                'step' => 0.1,
            ]
        );

        $this->add_control(
            'slides_tablet',
            [
                'label' => __('Tablet Slides', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 4,
                'step' => 0.1,
            ]
        );

        $this->add_control(
            'slides_mobile',
            [
                'label' => __('Mobile Slides', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1.15,
                'min' => 1,
                'max' => 2,
                'step' => 0.05,
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
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

        $this->add_control(
            'slides_per_group',
            [
                'label' => __('Slides Per Group', 'advanced-carousel-pro'),
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

        $this->add_control(
            'autoplay_pause_on_interaction',
            [
                'label' => __('Pause Autoplay On Interaction', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'autoplay' => 'yes',
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
            'allow_touch_move',
            [
                'label' => __('Enable Drag/Swipe', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

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
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'mousewheel_sensitivity',
            [
                'label' => __('Mousewheel Sensitivity', 'advanced-carousel-pro'),
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
            'loop',
            [
                'label' => __('Loop', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

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
            'show_arrows',
            [
                'label' => __('Show Arrows', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_dots',
            [
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
                'condition' => [
                    'show_dots' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dynamic_bullets',
            [
                'label' => __('Dynamic Bullets', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'show_dots' => 'yes',
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
                    'show_dots' => 'yes',
                    'pagination_type' => 'bullets',
                    'dynamic_bullets' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'rewind',
            [
                'label' => __('Rewind (When Loop Is Off)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'loop!' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_wrapper',
            [
                'label' => __('Wrapper', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'wrapper_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .pce-v5-wrapper',
            ]
        );

        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label' => __('Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wrapper_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'wrapper_border',
                'selector' => '{{WRAPPER}} .pce-v5-wrapper',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_card',
            [
                'label' => __('Card', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg',
            [
                'label' => __('Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .pce-v5-card',
            ]
        );

        $this->add_control(
            'card_hover_heading',
            [
                'label' => __('Hover Effects', 'advanced-carousel-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'card_hover_translate',
            [
                'label' => __('Lift On Hover (Y)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -40,
                        'max' => 40,
                    ],
                ],
                'default' => [
                    'size' => -4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-hover-translate: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_hover_scale',
            [
                'label' => __('Scale On Hover', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0.9,
                'max' => 1.1,
                'step' => 0.01,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-hover-scale: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_rotate',
            [
                'label' => __('Rotate On Hover (deg)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => -8,
                'max' => 8,
                'step' => 0.1,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-hover-rotate: {{VALUE}}deg;',
                ],
            ]
        );

        $this->add_control(
            'card_hover_transition',
            [
                'label' => __('Hover Transition Duration (ms)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 250,
                'min' => 100,
                'max' => 1200,
                'step' => 10,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-transition: {{VALUE}}ms;',
                    '{{WRAPPER}} .pce-v5-media img' => '--pce-card-image-transition: {{VALUE}}ms;',
                ],
            ]
        );

        $this->add_control(
            'card_image_hover_scale',
            [
                'label' => __('Image Zoom On Hover', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1.04,
                'min' => 1,
                'max' => 1.4,
                'step' => 0.01,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-image-hover-scale: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_bg',
            [
                'label' => __('Hover Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_border',
            [
                'label' => __('Hover Border Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_hover_shadow',
                'selector' => '{{WRAPPER}} .pce-v5-card:hover',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_image',
            [
                'label' => __('Image', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_ratio',
            [
                'label' => __('Aspect Ratio', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => '1 / 1',
                'options' => [
                    '1 / 1' => __('Square (1:1)', 'advanced-carousel-pro'),
                    '4 / 5' => __('Portrait (4:5)', 'advanced-carousel-pro'),
                    '3 / 4' => __('Portrait (3:4)', 'advanced-carousel-pro'),
                    '16 / 9' => __('Landscape (16:9)', 'advanced-carousel-pro'),
                    'auto' => __('Auto', 'advanced-carousel-pro'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-media' => 'aspect-ratio: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'image_fit',
            [
                'label' => __('Object Fit', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'advanced-carousel-pro'),
                    'contain' => __('Contain', 'advanced-carousel-pro'),
                    'fill' => __('Fill', 'advanced-carousel-pro'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-media img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .pce-v5-media',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_shadow',
                'selector' => '{{WRAPPER}} .pce-v5-media',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_text',
            [
                'label' => __('Text', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __('Alignment', 'advanced-carousel-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-body' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .pce-v5-title',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Price Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .pce-v5-price',
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label' => __('Description Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'selector' => '{{WRAPPER}} .pce-v5-desc',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_badge',
            [
                'label' => __('Badge', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label' => __('Text Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_bg_color',
            [
                'label' => __('Background Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'selector' => '{{WRAPPER}} .pce-v5-badge',
            ]
        );

        $this->add_responsive_control(
            'badge_padding',
            [
                'label' => __('Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_offset_top',
            [
                'label' => __('Top Offset', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -40,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_offset_inline',
            [
                'label' => __('Side Offset', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -40,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'inset-inline-end: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'badge_icon_position',
            [
                'label' => __('Icon Position', 'advanced-carousel-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => __('Before', 'advanced-carousel-pro'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'row-reverse' => [
                        'title' => __('After', 'advanced-carousel-pro'),
                        'icon' => 'eicon-arrow-right',
                    ],
                ],
                'default' => 'row',
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_icon_gap',
            [
                'label' => __('Icon Spacing', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 24,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'badge_icon_color',
            [
                'label' => __('Icon Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge .pce-v5-badge-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-badge .pce-v5-badge-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_icon_size',
            [
                'label' => __('Icon Size', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge .pce-v5-badge-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pce-v5-badge .pce-v5-badge-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_arrows',
            [
                'label' => __('Arrows', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Arrow Button Size', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 24,
                        'max' => 90,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_icon_size',
            [
                'label' => __('Arrow Icon Size', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 42,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav span' => 'font-size: {{SIZE}}{{UNIT}}; line-height: 1;',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_position_top',
            [
                'label' => __('Vertical Position', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 400,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-navigation' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_position_sides',
            [
                'label' => __('Horizontal Offset', 'advanced-carousel-pro'),
                'description' => __('Use negative values to push arrows outside the carousel.', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -120,
                        'max' => 120,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-navigation' => 'left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_wrapper_padding',
            [
                'label' => __('Arrows Wrapper Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-navigation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_button_margin',
            [
                'label' => __('Arrow Button Margin', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_button_padding',
            [
                'label' => __('Arrow Button Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('arrows_color_tabs');

        $this->start_controls_tab(
            'arrows_normal_tab',
            [
                'label' => __('Normal', 'advanced-carousel-pro'),
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => __('Icon Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color',
            [
                'label' => __('Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'arrows_hover_tab',
            [
                'label' => __('Hover', 'advanced-carousel-pro'),
            ]
        );

        $this->add_control(
            'arrows_color_hover',
            [
                'label' => __('Icon Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_hover',
            [
                'label' => __('Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrows_border',
                'selector' => '{{WRAPPER}} .pce-v5-nav',
            ]
        );

        $this->add_responsive_control(
            'arrows_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'arrows_shadow',
                'selector' => '{{WRAPPER}} .pce-v5-nav',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_dots',
            [
                'label' => __('Dots', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label' => __('Dot Size', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 4,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_active_width',
            [
                'label' => __('Active Dot Width', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 4,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_spacing',
            [
                'label' => __('Dots Spacing', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_top_margin',
            [
                'label' => __('Top Margin', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 120,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination.swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_alignment',
            [
                'label' => __('Alignment', 'advanced-carousel-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination.swiper-pagination' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => __('Dot Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_color_active',
            [
                'label' => __('Active Dot Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_button',
            [
                'label' => __('Button', 'advanced-carousel-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'btn_alignment',
            [
                'label' => __('Alignment', 'advanced-carousel-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'advanced-carousel-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn-wrapper' => 'display: flex; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_width_type',
            [
                'label' => __('Width', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'full' => __('Full Width', 'advanced-carousel-pro'),
                    'auto' => __('Auto', 'advanced-carousel-pro'),
                    'custom' => __('Custom', 'advanced-carousel-pro'),
                ],
                'selectors_dictionary' => [
                    'full' => 'width: 100%;',
                    'auto' => 'width: auto;',
                    'custom' => 'width: auto;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_custom_width',
            [
                'label' => __('Custom Width', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 60,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'btn_width_type' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_min_height',
            [
                'label' => __('Minimum Height', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 24,
                        'max' => 120,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_icon_size',
            [
                'label' => __('Icon Size', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 48,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pce-v5-btn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_icon_gap',
            [
                'label' => __('Icon Gap', 'advanced-carousel-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'btn_typography',
                'selector' => '{{WRAPPER}} .pce-v5-btn',
            ]
        );

        $this->add_responsive_control(
            'btn_padding',
            [
                'label' => __('Padding', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_radius',
            [
                'label' => __('Border Radius', 'advanced-carousel-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('btn_state_tabs');

        $this->start_controls_tab(
            'btn_normal_tab',
            [
                'label' => __('Normal', 'advanced-carousel-pro'),
            ]
        );

        $this->add_control(
            'btn_color',
            [
                'label' => __('Text Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_bg',
            [
                'label' => __('Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_border_color',
            [
                'label' => __('Border Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'btn_hover_tab',
            [
                'label' => __('Hover', 'advanced-carousel-pro'),
            ]
        );

        $this->add_control(
            'btn_color_hover',
            [
                'label' => __('Text Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:hover, {{WRAPPER}} .pce-v5-btn:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn:hover .pce-v5-btn-icon, {{WRAPPER}} .pce-v5-btn:focus .pce-v5-btn-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn:hover .pce-v5-btn-icon svg, {{WRAPPER}} .pce-v5-btn:focus .pce-v5-btn-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_bg_hover',
            [
                'label' => __('Background', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:hover, {{WRAPPER}} .pce-v5-btn:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_border_color_hover',
            [
                'label' => __('Border Color', 'advanced-carousel-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:hover, {{WRAPPER}} .pce-v5-btn:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_hover_translate',
            [
                'label' => __('Hover Lift (Y)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => -1,
                'min' => -20,
                'max' => 20,
                'step' => 0.5,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => '--pce-btn-hover-translate: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'btn_hover_scale',
            [
                'label' => __('Hover Scale', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0.9,
                'max' => 1.1,
                'step' => 0.01,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => '--pce-btn-hover-scale: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_transition_duration',
            [
                'label' => __('Transition Duration (ms)', 'advanced-carousel-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 200,
                'min' => 100,
                'max' => 1200,
                'step' => 10,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => '--pce-btn-transition-duration: {{VALUE}}ms;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'btn_border',
                'selector' => '{{WRAPPER}} .pce-v5-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btn_shadow',
                'selector' => '{{WRAPPER}} .pce-v5-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btn_shadow_hover',
                'selector' => '{{WRAPPER}} .pce-v5-btn:hover, {{WRAPPER}} .pce-v5-btn:focus',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['items']) || !is_array($settings['items'])) {
            return;
        }

        $widget_id = 'pce-v5-' . $this->get_id();

        $slides_desktop = $this->sanitize_float($settings['slides_desktop'] ?? 3, 1, 6, 3);
        $slides_tablet = $this->sanitize_float($settings['slides_tablet'] ?? 2, 1, 4, 2);
        $slides_mobile = $this->sanitize_float($settings['slides_mobile'] ?? 1.15, 1, 2, 1.15);
        $gap = $this->sanitize_int($settings['space_between']['size'] ?? 20, 0, 60, 20);
        $autoplay_delay = $this->sanitize_int($settings['autoplay_delay'] ?? 3500, 1000, 15000, 3500);
        $title_tag = $this->sanitize_choice($settings['title_html_tag'] ?? 'h3', ['h2', 'h3', 'h4', 'div'], 'h3');

        $slider_options = [
            'desktop' => $slides_desktop,
            'tablet' => $slides_tablet,
            'mobile' => $slides_mobile,
            'gap' => $gap,
            'slidesPerGroup' => $this->sanitize_int($settings['slides_per_group'] ?? 1, 1, 6, 1),
            'centeredSlides' => ($settings['centered_slides'] ?? '') === 'yes',
            'effect' => $this->sanitize_choice($settings['slider_effect'] ?? 'slide', ['slide', 'fade', 'coverflow', 'cards', 'creative'], 'slide'),
            'autoplay' => ($settings['autoplay'] ?? '') === 'yes',
            'autoplayDelay' => $autoplay_delay,
            'autoplayReverse' => ($settings['autoplay_reverse'] ?? '') === 'yes',
            'autoplayPauseOnInteraction' => ($settings['autoplay_pause_on_interaction'] ?? '') === 'yes',
            'flowDirection' => in_array(($settings['flow_direction'] ?? 'auto'), ['auto', 'ltr', 'rtl'], true) ? $settings['flow_direction'] : 'auto',
            'loop' => ($settings['loop'] ?? '') === 'yes',
            'rewind' => ($settings['rewind'] ?? 'yes') === 'yes',
            'pauseOnHover' => ($settings['pause_on_hover'] ?? '') === 'yes',
            'showArrows' => ($settings['show_arrows'] ?? '') === 'yes',
            'showDots' => ($settings['show_dots'] ?? '') === 'yes',
            'paginationType' => $this->sanitize_choice($settings['pagination_type'] ?? 'bullets', ['bullets', 'fraction', 'progressbar'], 'bullets'),
            'dynamicBullets' => ($settings['dynamic_bullets'] ?? '') === 'yes',
            'dynamicMainBullets' => $this->sanitize_int($settings['dynamic_main_bullets'] ?? 1, 1, 10, 1),
            'speed' => $this->sanitize_int($settings['transition_speed'] ?? 550, 100, 3000, 550),
            'allowTouchMove' => ($settings['allow_touch_move'] ?? 'yes') === 'yes',
            'dragThreshold' => $this->sanitize_int($settings['drag_threshold'] ?? 8, 0, 60, 8),
            'mousewheel' => ($settings['mousewheel_control'] ?? '') === 'yes',
            'mousewheelSensitivity' => $this->sanitize_float($settings['mousewheel_sensitivity'] ?? 1, 0.1, 5, 1),
            'mousewheelReleaseOnEdges' => ($settings['mousewheel_release_on_edges'] ?? 'yes') === 'yes',
            'keyboard' => ($settings['keyboard_control'] ?? 'yes') === 'yes',
            'respectReducedMotion' => ($settings['respect_reduced_motion'] ?? 'yes') === 'yes',
        ];
        $nav_class = $slider_options['showArrows'] ? 'pce-v5-navigation' : 'pce-v5-navigation is-hidden';
        $dots_class = $slider_options['showDots'] ? 'swiper-pagination pce-v5-pagination pce-v5-pagination-' . $slider_options['paginationType'] : 'swiper-pagination pce-v5-pagination is-hidden';

        ?>
        <div
            id="<?php echo esc_attr($widget_id); ?>"
            class="pce-v5-wrapper"
            data-widget-id="<?php echo esc_attr($widget_id); ?>"
            data-settings="<?php echo esc_attr(wp_json_encode($slider_options)); ?>"
        >
            <div class="swiper pce-v5-slider" aria-label="<?php echo esc_attr__('Product carousel', 'advanced-carousel-pro'); ?>">
                <div class="swiper-wrapper">
                    <?php foreach ($settings['items'] as $index => $item) : ?>
                        <?php
                        $title = isset($item['title']) ? trim((string) $item['title']) : '';
                        $badge = isset($item['category']) ? trim((string) $item['category']) : '';
                        $price = isset($item['price']) ? trim((string) $item['price']) : '';
                        $desc = isset($item['desc']) ? trim((string) $item['desc']) : '';
                        $btn_text = isset($item['btn_text']) && $item['btn_text'] !== '' ? $item['btn_text'] : __('View Product', 'advanced-carousel-pro');
                        $btn_icon_position = isset($item['btn_icon_position']) && $item['btn_icon_position'] === 'after' ? 'after' : 'before';
                        $has_btn_icon = !empty($item['btn_icon']['value']);
                        $img_url = !empty($item['image']['url']) ? $item['image']['url'] : Utils::get_placeholder_image_src();
                        $alt_text = $this->get_image_alt($item, $title);

                        $link_key = 'item_link_' . $index;
                        $has_link = !empty($item['link']['url']);
                        $button_classes = 'pce-v5-btn';

                        if ($has_btn_icon) {
                            $button_classes .= ' has-icon';
                            $button_classes .= $btn_icon_position === 'after' ? ' icon-after' : ' icon-before';
                        }

                        if ($has_link) {
                            $this->add_link_attributes($link_key, $item['link']);
                            $this->add_render_attribute($link_key, 'class', $button_classes);
                        }
                        ?>
                        <article class="swiper-slide">
                            <div class="pce-v5-card">
                                <?php if ($badge !== '') : ?>
                                    <span class="pce-v5-badge">
                                        <?php if (!empty($item['badge_icon']['value'])) : ?>
                                            <span class="pce-v5-badge-icon" aria-hidden="true">
                                                <?php Icons_Manager::render_icon($item['badge_icon'], ['aria-hidden' => 'true']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="pce-v5-badge-text"><?php echo esc_html($badge); ?></span>
                                    </span>
                                <?php endif; ?>

                                <div class="pce-v5-media">
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($alt_text); ?>" loading="lazy" />
                                </div>

                                <div class="pce-v5-body">
                                    <?php if ($title !== '') : ?>
                                        <<?php echo esc_attr($title_tag); ?> class="pce-v5-title"><?php echo esc_html($title); ?></<?php echo esc_attr($title_tag); ?>>
                                    <?php endif; ?>

                                    <?php if ($price !== '') : ?>
                                        <p class="pce-v5-price"><?php echo esc_html($price); ?></p>
                                    <?php endif; ?>

                                    <?php if ($desc !== '') : ?>
                                        <p class="pce-v5-desc"><?php echo esc_html($desc); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="pce-v5-btn-wrapper">
                                    <?php if ($has_link) : ?>
                                        <a <?php echo $this->get_render_attribute_string($link_key); ?>>
                                            <?php if ($has_btn_icon) : ?>
                                                <span class="pce-v5-btn-icon" aria-hidden="true"><?php Icons_Manager::render_icon($item['btn_icon'], ['aria-hidden' => 'true']); ?></span>
                                            <?php endif; ?>
                                            <span class="pce-v5-btn-label"><?php echo esc_html($btn_text); ?></span>
                                        </a>
                                    <?php else : ?>
                                        <span class="<?php echo esc_attr($button_classes); ?> is-disabled" aria-disabled="true">
                                            <?php if ($has_btn_icon) : ?>
                                                <span class="pce-v5-btn-icon" aria-hidden="true"><?php Icons_Manager::render_icon($item['btn_icon'], ['aria-hidden' => 'true']); ?></span>
                                            <?php endif; ?>
                                            <span class="pce-v5-btn-label"><?php echo esc_html($btn_text); ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="<?php echo esc_attr($nav_class); ?>">
                <button type="button" class="pce-v5-nav pce-v5-prev" aria-label="<?php echo esc_attr__('Previous slide', 'advanced-carousel-pro'); ?>">
                    <span>&larr;</span>
                </button>
                <button type="button" class="pce-v5-nav pce-v5-next" aria-label="<?php echo esc_attr__('Next slide', 'advanced-carousel-pro'); ?>">
                    <span>&rarr;</span>
                </button>
            </div>

            <div class="<?php echo esc_attr($dots_class); ?>"></div>
        </div>
        <?php
    }

    private function sanitize_int($value, $min, $max, $fallback) {
        $value = (int) $value;

        if ($value < $min || $value > $max) {
            return (int) $fallback;
        }

        return $value;
    }

    private function sanitize_float($value, $min, $max, $fallback) {
        $value = (float) $value;

        if ($value < $min || $value > $max) {
            return (float) $fallback;
        }

        return $value;
    }

    private function sanitize_choice($value, $allowed_values, $fallback) {
        if (in_array($value, $allowed_values, true)) {
            return $value;
        }

        return $fallback;
    }

    private function get_image_alt($item, $fallback) {
        if (!empty($item['image']['id'])) {
            $meta_alt = get_post_meta((int) $item['image']['id'], '_wp_attachment_image_alt', true);
            if (is_string($meta_alt) && trim($meta_alt) !== '') {
                return $meta_alt;
            }
        }

        if (is_string($fallback) && $fallback !== '') {
            return $fallback;
        }

        return __('Product image', 'advanced-carousel-pro');
    }
}
