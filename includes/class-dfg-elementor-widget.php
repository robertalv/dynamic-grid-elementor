<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class DFG_Filterable_Gallery_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'dfg_filterable_gallery';
    }
    public function get_title() {
        return __( 'Dynamic Filter Gallery', 'dynamic-filter-gallery' );
    }
    public function get_icon() {
        return 'eicon-gallery-grid';
    }
    public function get_categories() {
        return [ 'general' ];
    }
    public function _register_controls() {
        $this->start_controls_section(
            'section_gallery',
            [
                'label' => __( 'Gallery Items', 'dynamic-filter-gallery' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'items',
            [
                'label' => __( 'Gallery Items', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'image',
                        'label' => __( 'Image', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'default' => [
                            'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'name' => 'title',
                        'label' => __( 'Title', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => __( 'Gallery Item', 'dynamic-filter-gallery' ),
                    ],
                    [
                        'name' => 'category',
                        'label' => __( 'Category', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => __( 'Category', 'dynamic-filter-gallery' ),
                    ],
                    [
                        'name' => 'link_type',
                        'label' => __( 'Link Type', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'none' => __( 'None', 'dynamic-filter-gallery' ),
                            'post' => __( 'Post/Page/Custom Post', 'dynamic-filter-gallery' ),
                            'custom' => __( 'Custom URL', 'dynamic-filter-gallery' ),
                        ],
                        'default' => 'none',
                    ],
                    [
                        'name' => 'link_post',
                        'label' => __( 'Select Post/Page', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'options' => $this->dfg_get_all_posts_for_elementor(),
                        'condition' => [ 'link_type' => 'post' ],
                        'selectors' => [
                            '{{WRAPPER}} .dfg-link-post-select' => 'min-width: 300px; width: 300px; max-width: 100%;',
                        ],
                        'attributes' => [
                            'class' => 'dfg-link-post-select',
                        ],
                    ],
                    [
                        'name' => 'link_url',
                        'label' => __( 'Custom URL', 'dynamic-filter-gallery' ),
                        'type' => \Elementor\Controls_Manager::URL,
                        'placeholder' => 'https://',
                        'condition' => [ 'link_type' => 'custom' ],
                    ],
                ],
                'default' => [],
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();

        // Filter Button Style Section
        $this->start_controls_section(
            'section_filter_button_style',
            [
                'label' => __( 'Filter Button Style', 'dynamic-filter-gallery' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'filter_btn_bg_color',
            [
                'label' => __( 'Background Color', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dfg-filter-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'filter_btn_text_color',
            [
                'label' => __( 'Text Color', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dfg-filter-btn' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'filter_btn_text_transform',
            [
                'label' => __( 'Text Transform', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'dynamic-filter-gallery' ),
                    'uppercase' => __( 'UPPERCASE', 'dynamic-filter-gallery' ),
                    'lowercase' => __( 'lowercase', 'dynamic-filter-gallery' ),
                    'capitalize' => __( 'Capitalize', 'dynamic-filter-gallery' ),
                ],
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .dfg-filter-btn' => 'text-transform: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'filter_btn_letter_spacing',
            [
                'label' => __( 'Letter Spacing', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 10 ],
                    'em' => [ 'min' => 0, 'max' => 1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dfg-filter-btn' => 'letter-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        // Gallery Image Style Section
        $this->start_controls_section(
            'section_gallery_image_style',
            [
                'label' => __( 'Gallery Image Style', 'dynamic-filter-gallery' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-grid' => '--dfg-columns: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'gap',
            [
                'label' => __( 'Gap Between Items (px)', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'default' => 20,
                'tablet_default' => 16,
                'mobile_default' => 12,
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-grid' => 'gap: {{VALUE}}px;',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_height',
            [
                'label' => __( 'Image Height (px)', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 50,
                'max' => 800,
                'default' => 160,
                'tablet_default' => 140,
                'mobile_default' => 120,
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-bg' => 'height: {{VALUE}}px;',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Image Border Radius (px)', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 50,
                'default' => 8,
                'tablet_default' => 8,
                'mobile_default' => 8,
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-item img' => 'border-radius: {{VALUE}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'gallery_item_border',
                'label' => __( 'Gallery Item Border', 'dynamic-filter-gallery' ),
                'selector' => '{{WRAPPER}} .dfg-gallery-item',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'gallery_item_hover_border',
                'label' => __( 'Hover Border', 'dynamic-filter-gallery' ),
                'selector' => '{{WRAPPER}} .dfg-gallery-item:hover',
            ]
        );
        $this->add_control(
            'gallery_item_hover_border_color',
            [
                'label' => __( 'Hover Border Color', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'gallery_item_hover_border_width',
            [
                'label' => __( 'Hover Border Width', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 20 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-item:hover' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'gallery_hover_border_transition_duration',
            [
                'label' => __( 'Hover Border Transition Duration', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms' ],
                'range' => [
                    's' => [ 'min' => 0, 'max' => 3, 'step' => 0.05 ],
                    'ms' => [ 'min' => 0, 'max' => 3000, 'step' => 10 ],
                ],
                'default' => [ 'size' => 1, 'unit' => 's' ],
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-item:hover, {{WRAPPER}} .dfg-gallery-item:hover::after' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'gallery_hover_border_transition_timing',
            [
                'label' => __( 'Hover Border Transition Timing Function', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'ease' => 'ease',
                    'linear' => 'linear',
                    'ease-in' => 'ease-in',
                    'ease-out' => 'ease-out',
                    'ease-in-out' => 'ease-in-out',
                    'cubic-bezier(0.4,0,0.2,1)' => 'cubic-bezier(0.4,0,0.2,1)',
                ],
                'default' => 'ease',
                'selectors' => [
                    '{{WRAPPER}} .dfg-gallery-item:hover, {{WRAPPER}} .dfg-gallery-item:hover::after' => 'transition-timing-function: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        // Gallery Title Style Section
        $this->start_controls_section(
            'section_gallery_title_style',
            [
                'label' => __( 'Gallery Title Style', 'dynamic-filter-gallery' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'gallery_title_typography',
                'label' => __( 'Typography', 'dynamic-filter-gallery' ),
                'selector' => '{{WRAPPER}} .gallery-text',
            ]
        );
        $this->add_control(
            'gallery_title_color',
            [
                'label' => __( 'Text Color', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gallery-text' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'gallery_title_background',
                'label' => __( 'Background', 'dynamic-filter-gallery' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .gallery-text',
            ]
        );
        $this->add_control(
            'gallery_title_padding',
            [
                'label' => __( 'Padding', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gallery-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'gallery_title_border_radius',
            [
                'label' => __( 'Border Radius', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gallery-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'gallery_title_letter_spacing',
            [
                'label' => __( 'Letter Spacing', 'dynamic-filter-gallery' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 10 ],
                    'em' => [ 'min' => 0, 'max' => 1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gallery-text' => 'letter-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    public function render() {
        $settings = $this->get_settings_for_display();
        if ( empty( $settings['items'] ) ) {
            return;
        }
        // Output dynamic style for hover border transition on ::after
        $transition_duration = isset($settings['gallery_hover_border_transition_duration']['size']) ? $settings['gallery_hover_border_transition_duration']['size'] : 1;
        $transition_unit = isset($settings['gallery_hover_border_transition_duration']['unit']) ? $settings['gallery_hover_border_transition_duration']['unit'] : 's';
        $transition_timing = isset($settings['gallery_hover_border_transition_timing']) ? $settings['gallery_hover_border_transition_timing'] : 'ease';
        $unique_id = 'dfg-' . uniqid();
        echo '<style>.' . $unique_id . '.dfg-gallery-item:hover::after {';
        echo 'transition-property: border;';
        echo 'transition-duration: ' . esc_attr($transition_duration) . esc_attr($transition_unit) . ';';
        echo 'transition-timing-function: ' . esc_attr($transition_timing) . ';';
        echo '}</style>';
        // Add the unique class to each gallery item
        $columns = isset( $settings['columns'] ) ? $settings['columns'] : 3;
        $columns_tablet = isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 2;
        $columns_mobile = isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 1;
        // Get unique categories
        $categories = array_unique( array_map( function( $item ) {
            return $item['category'];
        }, $settings['items'] ) );
        echo '<div class="dfg-filters">';
        echo '<button class="dfg-filter-btn" data-filter="*">' . esc_html__( 'All', 'dynamic-filter-gallery' ) . '</button>';
        foreach ( $categories as $cat ) {
            $cat_slug = sanitize_title($cat);
            echo '<button class="dfg-filter-btn" data-filter="' . esc_attr( $cat_slug ) . '">' . esc_html( $cat ) . '</button>';
        }
        echo '</div>';
        echo '<div class="dfg-gallery-grid">';
        foreach ( $settings['items'] as $item ) {
            $cat_slug = sanitize_title($item['category']);
            $link = '';
            if (isset($item['link_type'])) {
                if ($item['link_type'] === 'post' && !empty($item['link_post'])) {
                    $link = get_permalink($item['link_post']);
                } elseif ($item['link_type'] === 'custom' && !empty($item['link_url']['url'])) {
                    $link = esc_url($item['link_url']['url']);
                }
            }
            $image_url = esc_url($item['image']['url']);
            $title = esc_html($item['title']);
            echo '<div class="dfg-gallery-item ' . esc_attr($cat_slug) . ' ' . esc_attr($unique_id) . '" data-category="' . esc_attr( $cat_slug ) . '">';
            if ($link) {
                echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener" class="dfg-gallery-link">';
            }
            echo '<div class="dfg-gallery-bg" style="background-image: url(' . $image_url . ');">';
            echo '<img src="' . $image_url . '" alt="' . $title . '" class="dfg-gallery-img-fallback" />';
            echo '</div>';
            echo '<span class="gallery-text">' . $title . '</span>';
            if ($link) {
                echo '</a>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    public function get_script_depends() {
        return [ 'dfg-filterable-gallery-js' ];
    }
    public function get_style_depends() {
        return [ 'dfg-filterable-gallery-css' ];
    }
    public function dfg_get_all_posts_for_elementor() {
        $args = array(
            'post_type'      => 'any',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        $posts = get_posts($args);
        $options = [];
        foreach ($posts as $post) {
            $options[$post->ID] = esc_html(get_the_title($post->ID)) . ' (' . esc_html($post->post_type) . ')';
        }
        return $options;
    }
} 