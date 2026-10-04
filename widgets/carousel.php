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
        return __('Nexa Slider', 'nexa-slider');
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['nexa', 'carousel', 'product', 'slider', 'elementor', 'woocommerce', 'نکسا', 'اسلایدر', 'محصول', 'ووکامرس'];
    }

    public function get_script_depends() {
        return ['pce-v5-script'];
    }

    public function get_style_depends() {
        return ['pce-v5-style'];
    }

    protected function is_dynamic_content(): bool {
        // Product prices and stock must not be frozen by Elementor element caching.
        return true;
    }

    protected function register_controls() {
        require ACP_PLUGIN_PATH . 'includes/controls/content.php';

        $this->start_controls_section(
            'style_wrapper',
            [
                'label' => __('Wrapper', 'nexa-slider'),
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
                'label' => __('Padding', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wrapper_radius',
            [
                'label' => __('Border Radius', 'nexa-slider'),
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
                'label' => __('Card', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('card_preset', [
            'label' => __('Card Preset', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'default',
            'options' => ['default' => __('Classic', 'nexa-slider'), 'minimal' => __('Minimal', 'nexa-slider'), 'catalog' => __('Catalog', 'nexa-slider')],
            'description' => __('Style controls can further customize the selected preset.', 'nexa-slider'),
        ]);

        $this->add_control(
            'card_bg',
            [
                'label' => __('Background', 'nexa-slider'),
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
                'label' => __('Padding', 'nexa-slider'),
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
                'label' => __('Border Radius', 'nexa-slider'),
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
                'label' => __('Hover Effects', 'nexa-slider'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'card_hover_translate',
            [
                'label' => __('Lift On Hover (Y)', 'nexa-slider'),
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
                    '{{WRAPPER}} .pce-v5-slider' => '--pce-hover-lift: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_hover_scale',
            [
                'label' => __('Scale On Hover', 'nexa-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0.9,
                'max' => 1.1,
                'step' => 0.01,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card' => '--pce-card-hover-scale: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-slider' => '--pce-hover-scale: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_rotate',
            [
                'label' => __('Rotate On Hover (deg)', 'nexa-slider'),
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
                'label' => __('Hover Transition Duration (ms)', 'nexa-slider'),
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
                'label' => __('Image Zoom On Hover', 'nexa-slider'),
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
                'label' => __('Hover Background', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-card:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_border',
            [
                'label' => __('Hover Border Color', 'nexa-slider'),
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
                'label' => __('Image', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('image_size', [
            'label' => __('Image Resolution', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'medium_large',
            'options' => ['thumbnail' => __('Thumbnail', 'nexa-slider'), 'medium' => __('Medium', 'nexa-slider'), 'medium_large' => __('Medium large', 'nexa-slider'), 'large' => __('Large', 'nexa-slider'), 'full' => __('Full', 'nexa-slider')],
        ]);

        $this->add_control(
            'image_ratio',
            [
                'label' => __('Aspect Ratio', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => '1 / 1',
                'options' => [
                    '1 / 1' => __('Square (1:1)', 'nexa-slider'),
                    '4 / 5' => __('Portrait (4:5)', 'nexa-slider'),
                    '3 / 4' => __('Portrait (3:4)', 'nexa-slider'),
                    '16 / 9' => __('Landscape (16:9)', 'nexa-slider'),
                    'auto' => __('Auto', 'nexa-slider'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-media' => 'aspect-ratio: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'image_fit',
            [
                'label' => __('Object Fit', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'nexa-slider'),
                    'contain' => __('Contain', 'nexa-slider'),
                    'fill' => __('Fill', 'nexa-slider'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-media img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_control('image_position', [
            'label' => __('Object Position', 'nexa-slider'), 'type' => Controls_Manager::SELECT, 'default' => 'center center',
            'options' => [
                'center center' => __('Center', 'nexa-slider'), 'center top' => __('Top', 'nexa-slider'), 'center bottom' => __('Bottom', 'nexa-slider'),
                'left center' => __('Left', 'nexa-slider'), 'right center' => __('Right', 'nexa-slider'),
            ],
            'selectors' => ['{{WRAPPER}} .pce-v5-media img' => 'object-position: {{VALUE}};'],
        ]);

        $this->add_responsive_control('image_height', [
            'label' => __('Image Height', 'nexa-slider'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'vh'],
            'range' => ['px' => ['min' => 80, 'max' => 700], 'vh' => ['min' => 10, 'max' => 80]],
            'description' => __('Overrides the aspect ratio when set.', 'nexa-slider'),
            'selectors' => ['{{WRAPPER}} .pce-v5-media' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;'],
        ]);

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __('Border Radius', 'nexa-slider'),
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
                'label' => __('Text', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __('Alignment', 'nexa-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'nexa-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'nexa-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'nexa-slider'),
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
            'desc_max_lines',
            [
                'label' => __('Description Max Lines', 'nexa-slider'),
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

        $this->add_control('desc_full', [
            'label' => __('Show Full Description', 'nexa-slider'), 'type' => Controls_Manager::SWITCHER, 'default' => '',
            'selectors' => ['{{WRAPPER}} .pce-v5-desc' => 'display: block; -webkit-line-clamp: unset; overflow: visible;'],
        ]);

        $this->add_control('title_max_lines', [
            'label' => __('Title Max Lines', 'nexa-slider'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'step' => 1,
            'description' => __('Leave empty to show the whole title.', 'nexa-slider'),
            'selectors' => ['{{WRAPPER}} .pce-v5-title' => 'display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: {{VALUE}}; overflow: hidden;'],
        ]);

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'nexa-slider'),
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
                'label' => __('Price Color', 'nexa-slider'),
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
                'label' => __('Description Color', 'nexa-slider'),
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
                'label' => __('Badge', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label' => __('Text Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_bg_color',
            [
                'label' => __('Background Color', 'nexa-slider'),
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
                'label' => __('Padding', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_radius',
            [
                'label' => __('Border Radius', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_offset_top',
            [
                'label' => __('Top Offset', 'nexa-slider'),
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
                'label' => __('Side Offset', 'nexa-slider'),
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
                'label' => __('Icon Position', 'nexa-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => __('Before', 'nexa-slider'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'row-reverse' => [
                        'title' => __('After', 'nexa-slider'),
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
                'label' => __('Icon Spacing', 'nexa-slider'),
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
                'label' => __('Icon Color', 'nexa-slider'),
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
                'label' => __('Icon Size', 'nexa-slider'),
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
                'label' => __('Arrows', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Arrow Button Size', 'nexa-slider'),
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
                'label' => __('Arrow Icon Size', 'nexa-slider'),
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
                'label' => __('Vertical Position', 'nexa-slider'),
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
                'label' => __('Horizontal Offset', 'nexa-slider'),
                'description' => __('Use negative values to push arrows outside the carousel.', 'nexa-slider'),
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
                'label' => __('Arrows Wrapper Padding', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-navigation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_button_margin',
            [
                'label' => __('Arrow Button Margin', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_button_padding',
            [
                'label' => __('Arrow Button Padding', 'nexa-slider'),
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
                'label' => __('Normal', 'nexa-slider'),
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => __('Icon Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color',
            [
                'label' => __('Background', 'nexa-slider'),
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
                'label' => __('Hover', 'nexa-slider'),
            ]
        );

        $this->add_control(
            'arrows_color_hover',
            [
                'label' => __('Icon Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-nav:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_hover',
            [
                'label' => __('Background', 'nexa-slider'),
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
                'label' => __('Border Radius', 'nexa-slider'),
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
                'label' => __('Dots', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label' => __('Dot Size', 'nexa-slider'),
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
                'label' => __('Active Dot Width', 'nexa-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 4,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination:not(.swiper-pagination-bullets-dynamic) .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_spacing',
            [
                'label' => __('Dots Spacing', 'nexa-slider'),
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
                'label' => __('Top Margin', 'nexa-slider'),
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
                'label' => __('Alignment', 'nexa-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'nexa-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'nexa-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'nexa-slider'),
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
                'label' => __('Dot Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_color_active',
            [
                'label' => __('Active Dot Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-pagination .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control('fraction_color', [
            'label' => __('Counter Color', 'nexa-slider'), 'type' => Controls_Manager::COLOR,
            'condition' => ['pagination_type' => 'fraction'],
            'selectors' => ['{{WRAPPER}} .pce-v5-pagination-fraction' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'fraction_typography', 'selector' => '{{WRAPPER}} .pce-v5-pagination-fraction',
            'condition' => ['pagination_type' => 'fraction'],
        ]);
        $this->add_control('progress_height', [
            'label' => __('Progress Bar Height', 'nexa-slider'), 'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 1, 'max' => 16]], 'condition' => ['pagination_type' => 'progressbar'],
            'selectors' => ['{{WRAPPER}} .pce-v5-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('progress_track_color', [
            'label' => __('Progress Track Color', 'nexa-slider'), 'type' => Controls_Manager::COLOR,
            'condition' => ['pagination_type' => 'progressbar'],
            'selectors' => ['{{WRAPPER}} .pce-v5-pagination-progressbar' => 'background: {{VALUE}};'],
        ]);
        $this->add_control('progress_fill_color', [
            'label' => __('Progress Fill Color', 'nexa-slider'), 'type' => Controls_Manager::COLOR,
            'condition' => ['pagination_type' => 'progressbar'],
            'selectors' => ['{{WRAPPER}} .pce-v5-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}};'],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(
            'style_button',
            [
                'label' => __('Button', 'nexa-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'btn_alignment',
            [
                'label' => __('Alignment', 'nexa-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'nexa-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'nexa-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'nexa-slider'),
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
                'label' => __('Width', 'nexa-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'full' => __('Full Width', 'nexa-slider'),
                    'auto' => __('Auto', 'nexa-slider'),
                    'custom' => __('Custom', 'nexa-slider'),
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
                'label' => __('Custom Width', 'nexa-slider'),
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
                'label' => __('Minimum Height', 'nexa-slider'),
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
                'label' => __('Icon Size', 'nexa-slider'),
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
                'label' => __('Icon Gap', 'nexa-slider'),
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
                'label' => __('Padding', 'nexa-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'btn_radius',
            [
                'label' => __('Border Radius', 'nexa-slider'),
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
                'label' => __('Normal', 'nexa-slider'),
            ]
        );

        $this->add_control(
            'btn_color',
            [
                'label' => __('Text Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_bg',
            [
                'label' => __('Background', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_border_color',
            [
                'label' => __('Border Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary)' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'btn_hover_tab',
            [
                'label' => __('Hover', 'nexa-slider'),
            ]
        );

        $this->add_control(
            'btn_color_hover',
            [
                'label' => __('Text Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):hover, {{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn:hover .pce-v5-btn-icon, {{WRAPPER}} .pce-v5-btn:focus .pce-v5-btn-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pce-v5-btn:hover .pce-v5-btn-icon svg, {{WRAPPER}} .pce-v5-btn:focus .pce-v5-btn-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_bg_hover',
            [
                'label' => __('Background', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):hover, {{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_border_color_hover',
            [
                'label' => __('Border Color', 'nexa-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):hover, {{WRAPPER}} .pce-v5-btn:not(.pce-v5-btn-secondary):focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_hover_translate',
            [
                'label' => __('Hover Lift (Y)', 'nexa-slider'),
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
                'label' => __('Hover Scale', 'nexa-slider'),
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
                'label' => __('Transition Duration (ms)', 'nexa-slider'),
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

        $this->add_control('btn2_heading', ['label' => __('Second Button', 'nexa-slider'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        foreach ([
            'btn2_color' => [__('Text Color', 'nexa-slider'), '.pce-v5-btn-secondary', 'color'],
            'btn2_bg' => [__('Background', 'nexa-slider'), '.pce-v5-btn-secondary', 'background-color'],
            'btn2_border_color' => [__('Border Color', 'nexa-slider'), '.pce-v5-btn-secondary', 'border-color'],
            'btn2_color_hover' => [__('Text Color (Hover)', 'nexa-slider'), '.pce-v5-btn-secondary:hover, {{WRAPPER}} .pce-v5-btn-secondary:focus', 'color'],
            'btn2_bg_hover' => [__('Background (Hover)', 'nexa-slider'), '.pce-v5-btn-secondary:hover, {{WRAPPER}} .pce-v5-btn-secondary:focus', 'background-color'],
            'btn2_border_color_hover' => [__('Border Color (Hover)', 'nexa-slider'), '.pce-v5-btn-secondary:hover, {{WRAPPER}} .pce-v5-btn-secondary:focus', 'border-color'],
        ] as $control_id => [$label, $selector, $property]) {
            $this->add_control($control_id, [
                'label' => $label, 'type' => Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} ' . $selector => $property . ': {{VALUE}};'],
            ]);
        }

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

        $source = $this->sanitize_choice($settings['source'] ?? 'manual', ['manual', 'woocommerce', 'wordpress'], 'manual');
        $items = $source === 'woocommerce' ? PCE_Products::items($settings) : ($source === 'wordpress' ? PCE_Content::items($settings) : ($settings['items'] ?? []));
        $items = is_array($items) ? array_values(array_filter($items, 'is_array')) : [];
        if (!$items) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $message = $source === 'woocommerce' && !function_exists('wc_get_products')
                    ? __('Activate WooCommerce to display products.', 'nexa-slider')
                    : __('No items match the selected source. Add items or adjust product filters.', 'nexa-slider');
                echo '<div class="pce-v5-empty" role="status">' . esc_html($message) . '</div>';
            }
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
            'showAutoplayButton' => ($settings['show_autoplay_button'] ?? 'yes') === 'yes',
            'autoplayDelay' => $autoplay_delay,
            'autoplayReverse' => ($settings['autoplay_reverse'] ?? '') === 'yes',
            'autoplayPauseOnInteraction' => ($settings['autoplay_pause_on_interaction'] ?? '') === 'yes',
            'flowDirection' => $this->sanitize_choice($settings['flow_direction'] ?? 'auto', ['auto', 'ltr', 'rtl'], 'auto'),
            'loop' => ($settings['loop'] ?? 'yes') === 'yes',
            'rewind' => ($settings['rewind'] ?? 'yes') === 'yes',
            'pauseOnHover' => ($settings['pause_on_hover'] ?? 'yes') === 'yes',
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
        $slider_options['profiles'] = PCE_Settings::profiles($settings, $this->get_data('settings'));
        $slider_options['messages'] = [
            'prev' => __('Previous slide', 'nexa-slider'),
            'next' => __('Next slide', 'nexa-slider'),
            'first' => __('This is the first slide', 'nexa-slider'),
            'last' => __('This is the last slide', 'nexa-slider'),
            'bullet' => __('Go to slide {{index}}', 'nexa-slider'),
            'slide' => __('{{index}} of {{slidesLength}}', 'nexa-slider'),
            'pause' => __('Pause slideshow', 'nexa-slider'),
            'play' => __('Play slideshow', 'nexa-slider'),
            'reduced' => __('Slideshow paused: reduced motion', 'nexa-slider'),
        ];
        require ACP_PLUGIN_PATH . 'includes/templates/carousel.php';
    }

    private function get_image_html($item, $settings, $title) {
        $size = $this->sanitize_choice($settings['image_size'] ?? 'medium_large', ['thumbnail', 'medium', 'medium_large', 'large', 'full'], 'medium_large');
        $loading = ($settings['image_loading'] ?? 'lazy') === 'eager' ? 'eager' : 'lazy';
        $alt = $this->get_image_alt($item, $title);
        $image_id = (int) PCE_Settings::number($item['image']['id'] ?? '', 0, 1, PHP_INT_MAX);
        $image = $image_id ? wp_get_attachment_image($image_id, $size, false, ['alt' => $alt, 'loading' => $loading, 'decoding' => 'async']) : '';
        if ($image) {
            return $image;
        }
        $url = $image_id ? '' : esc_url(PCE_Settings::text($item['image']['url'] ?? ''));
        if (!$url) {
            $url = esc_url(Utils::get_placeholder_image_src());
        }
        return '<img src="' . $url . '" alt="' . esc_attr($alt) . '" loading="' . $loading . '" decoding="async" />';
    }

    /** Optional decorative second image; empty when the item has no valid hover image. */
    private function get_hover_image_html($item, $settings) {
        $hover = is_array($item['image_hover'] ?? null) ? $item['image_hover'] : [];
        $size = $this->sanitize_choice($settings['image_size'] ?? 'medium_large', ['thumbnail', 'medium', 'medium_large', 'large', 'full'], 'medium_large');
        $image_id = (int) PCE_Settings::number($hover['id'] ?? '', 0, 1, PHP_INT_MAX);
        $image = $image_id ? wp_get_attachment_image($image_id, $size, false, ['alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'class' => 'pce-v5-hover-image', 'aria-hidden' => 'true']) : '';
        if ($image) {
            return $image;
        }
        $url = esc_url_raw(PCE_Settings::text($hover['url'] ?? ''));
        if (!$url) {
            return '';
        }
        return '<img class="pce-v5-hover-image" src="' . esc_url($url) . '" alt="" aria-hidden="true" loading="lazy" decoding="async" />';
    }

    private function sanitize_int($value, $min, $max, $fallback) {
        return (int) PCE_Settings::number($value, $fallback, $min, $max);
    }

    private function sanitize_float($value, $min, $max, $fallback) {
        return (float) PCE_Settings::number($value, $fallback, $min, $max);
    }

    private function sanitize_choice($value, $allowed_values, $fallback) {
        if (in_array($value, $allowed_values, true)) {
            return $value;
        }

        return $fallback;
    }

    private function get_image_alt($item, $fallback) {
        if (!empty($item['image_alt']) && is_string($item['image_alt'])) {
            return $item['image_alt'];
        }
        if (is_numeric($item['image']['id'] ?? '') && (int) $item['image']['id'] > 0) {
            $meta_alt = get_post_meta((int) $item['image']['id'], '_wp_attachment_image_alt', true);
            if (is_string($meta_alt) && trim($meta_alt) !== '') {
                return $meta_alt;
            }
        }

        if (is_string($fallback) && $fallback !== '') {
            return $fallback;
        }

        return __('Product image', 'nexa-slider');
    }
}
