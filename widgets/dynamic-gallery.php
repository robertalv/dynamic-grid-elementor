<?php
class Dynamic_Gallery_Widget extends \Elementor\Widget_Base {

    private $categories = [];

    public function __construct($data = [], $args = null) {
        try {
            parent::__construct($data, $args);
            add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_scripts']);
        } catch (Throwable $e) {
            error_log('Dynamic_Gallery_Widget constructor error: ' . $e->getMessage());
        }
    }

    public function get_name() {
        return 'dynamic_gallery';
    }

    public function get_title() {
        return __('Dynamic Gallery', 'dynamic-gallery');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    public function editor_scripts() {
        wp_enqueue_script(
            'dynamic-gallery-editor',
            plugins_url('assets/js/editor.js', dirname(__FILE__)),
            ['jquery', 'elementor-editor'],
            '1.0.0',
            true
        );
    }

    protected function register_controls() {
        try {
            // Categories Section
            $this->start_controls_section(
                'categories_section',
                [
                    'label' => __('Categories', 'dynamic-gallery'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $repeater_categories = new \Elementor\Repeater();

            $repeater_categories->add_control(
                'category_name',
                [
                    'label' => __('Category Name', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => __('Enter category name', 'dynamic-gallery'),
                    'dynamic' => [
                        'active' => false,
                    ],
                ]
            );

            $this->add_control(
                'gallery_categories',
                [
                    'label' => __('Categories', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::REPEATER,
                    'fields' => $repeater_categories->get_controls(),
                    'default' => [
                        [
                            'category_name' => 'All',
                        ],
                    ],
                    'title_field' => '{{{ category_name }}}',
                ]
            );

            $this->add_control(
                'show_filters',
                [
                    'label' => __('Show Filter Buttons', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __('Show', 'dynamic-gallery'),
                    'label_off' => __('Hide', 'dynamic-gallery'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $this->end_controls_section();

            // Layout Settings
            $this->start_controls_section(
                'layout_section',
                [
                    'label' => __('Layout Settings', 'dynamic-gallery'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_responsive_control(
                'columns',
                [
                    'label' => __('Columns', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '3',
                    'tablet_default' => '2',
                    'mobile_default' => '1',
                    'options' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .boxxy-large' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    ],
                    'render_type' => 'template',
                ]
            );

            $this->add_responsive_control(
                'image_height',
                [
                    'label' => __('Image Height', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 200,
                            'max' => 800,
                            'step' => 10,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 400,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .boxxy-large__item' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'gap',
                [
                    'label' => __('Gap Between Items', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .boxxy-large' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            // Hover Effects Section
            $this->start_controls_section(
                'hover_effects_section',
                [
                    'label' => __('Hover Effects', 'dynamic-gallery'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'hover_effect',
                [
                    'label' => __('Hover Effect', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'frame',
                    'options' => [
                        'none' => __('None', 'dynamic-gallery'),
                        'frame' => __('Frame', 'dynamic-gallery'),
                        'zoom' => __('Zoom', 'dynamic-gallery'),
                        'fade' => __('Fade', 'dynamic-gallery'),
                    ],
                    'prefix_class' => 'hover-effect-',
                ]
            );

            $this->add_control(
                'show_item_name',
                [
                    'label' => __('Show Item Name', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __('Show', 'dynamic-gallery'),
                    'label_off' => __('Hide', 'dynamic-gallery'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'hover_effect' => 'frame',
                    ],
                ]
            );

            $this->add_control(
                'name_position',
                [
                    'label' => __('Name Position', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'bottom-left',
                    'options' => [
                        'top-left' => __('Top Left', 'dynamic-gallery'),
                        'top-right' => __('Top Right', 'dynamic-gallery'),
                        'bottom-left' => __('Bottom Left', 'dynamic-gallery'),
                        'bottom-right' => __('Bottom Right', 'dynamic-gallery'),
                    ],
                    'condition' => [
                        'show_item_name' => 'yes',
                        'hover_effect' => 'frame',
                    ],
                    'prefix_class' => 'name-position-',
                ]
            );

            $this->add_control(
                'name_spacing',
                [
                    'label' => __('Name Spacing', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'condition' => [
                        'show_item_name' => 'yes',
                        'hover_effect' => 'frame',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .gallery-item-name' => 'margin: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'name_typography',
                    'label' => __('Name Typography', 'dynamic-gallery'),
                    'selector' => '{{WRAPPER}} .gallery-item-name',
                    'condition' => [
                        'show_item_name' => 'yes',
                        'hover_effect' => 'frame',
                    ],
                ]
            );

            $this->add_control(
                'frame_spacing',
                [
                    'label' => __('Frame Spacing', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 30,
                    ],
                    'selectors' => [
                        '{{WRAPPER}}.hover-effect-frame .gallery-item:hover:before' => 'top: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'hover_effect' => 'frame',
                    ],
                ]
            );

            $this->add_control(
                'frame_border_width',
                [
                    'label' => __('Frame Border Width', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 10,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 2,
                    ],
                    'selectors' => [
                        '{{WRAPPER}}.hover-effect-frame .gallery-item:before' => 'border-width: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'hover_effect' => 'frame',
                    ],
                ]
            );

            $this->add_control(
                'frame_color',
                [
                    'label' => __('Frame Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}}.hover-effect-frame .gallery-item:before' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'hover_effect' => 'frame',
                    ],
                ]
            );

            $this->add_control(
                'overlay_color',
                [
                    'label' => __('Overlay Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => 'rgba(0,0,0,0.5)',
                    'selectors' => [
                        '{{WRAPPER}} .gallery-item:hover .gallery-item-overlay' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'content_position',
                [
                    'label' => __('Content Position', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'bottom',
                    'options' => [
                        'top' => __('Top', 'dynamic-gallery'),
                        'center' => __('Center', 'dynamic-gallery'),
                        'bottom' => __('Bottom', 'dynamic-gallery'),
                    ],
                    'prefix_class' => 'content-position-',
                ]
            );

            $this->add_responsive_control(
                'content_padding',
                [
                    'label' => __('Content Padding', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .gallery-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            // Gallery Items Section
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Gallery Items', 'dynamic-gallery'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $repeater = new \Elementor\Repeater();

            // Add desktop and mobile image fields
            $repeater->add_control(
                'desktop_image',
                [
                    'label' => __('Desktop Image', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'dynamic' => ['active' => true],
                ]
            );
            $repeater->add_control(
                'mobile_image',
                [
                    'label' => __('Mobile Image', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'dynamic' => ['active' => true],
                ]
            );
            // Fallback image field for compatibility
            $repeater->add_control(
                'image',
                [
                    'label' => __('Image (Fallback)', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'dynamic' => ['active' => true],
                ]
            );
            $repeater->add_control(
                'title',
                [
                    'label' => __('Title', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'dynamic' => ['active' => true],
                ]
            );
            // Dynamically populate category options from gallery_categories
            $category_options = ['cat-all' => __('All', 'dynamic-gallery')];
            $gallery_categories = $this->get_settings_for_display('gallery_categories');
            if (!empty($gallery_categories) && is_array($gallery_categories)) {
                foreach ($gallery_categories as $cat) {
                    if (!empty($cat['category_name']) && $cat['category_name'] !== 'All') {
                        $cat_key = 'cat-' . $cat['category_name'];
                        $category_options[$cat_key] = $cat['category_name'];
                    }
                }
            }
            $repeater->add_control(
                'category',
                [
                    'label' => __('Category', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'cat-all',
                    'options' => $category_options,
                    'dynamic' => ['active' => false],
                ]
            );
            $repeater->add_control(
                'url',
                [
                    'label' => __('Link URL', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => __('https://your-link.com', 'dynamic-gallery'),
                    'show_external' => true,
                    'default' => [
                        'url' => '',
                        'is_external' => false,
                        'nofollow' => false,
                    ],
                    'dynamic' => ['active' => true],
                ]
            );
            $this->add_control(
                'gallery_items',
                [
                    'label' => __('Items', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'prevent_empty' => false,
                    'title_field' => '{{{ title }}}',
                ]
            );

            $this->end_controls_section();

            // Style Section
            $this->start_controls_section(
                'style_section',
                [
                    'label' => __('Style', 'dynamic-gallery'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            // Filter Buttons Style
            $this->add_control(
                'filter_buttons_heading',
                [
                    'label' => __('Filter Buttons', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'filter_typography',
                    'label' => __('Typography', 'dynamic-gallery'),
                    'selector' => '{{WRAPPER}} .filterbox__item',
                ]
            );

            $this->add_responsive_control(
                'filter_spacing',
                [
                    'label' => __('Spacing Between', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 5,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'margin: 0 {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'filter_padding',
                [
                    'label' => __('Padding', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em'],
                    'default' => [
                        'top' => 8,
                        'right' => 16,
                        'bottom' => 8,
                        'left' => 16,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'filter_border',
                    'label' => __('Border', 'dynamic-gallery'),
                    'selector' => '{{WRAPPER}} .filterbox__item',
                ]
            );

            $this->add_control(
                'filter_border_radius',
                [
                    'label' => __('Border Radius', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'default' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            // Normal State
            $this->start_controls_tabs('filter_button_styles');

            $this->start_controls_tab(
                'filter_normal',
                [
                    'label' => __('Normal', 'dynamic-gallery'),
                ]
            );

            $this->add_control(
                'filter_color',
                [
                    'label' => __('Text Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#333333',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_background',
                [
                    'label' => __('Background Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Hover State
            $this->start_controls_tab(
                'filter_hover',
                [
                    'label' => __('Hover', 'dynamic-gallery'),
                ]
            );

            $this->add_control(
                'filter_color_hover',
                [
                    'label' => __('Text Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_background_hover',
                [
                    'label' => __('Background Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#333333',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_border_color_hover',
                [
                    'label' => __('Border Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'filter_border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_hover_transition',
                [
                    'label' => __('Transition Duration', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 3,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'size' => 0.3,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item' => 'transition-duration: {{SIZE}}s',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Active State
            $this->start_controls_tab(
                'filter_active',
                [
                    'label' => __('Active', 'dynamic-gallery'),
                ]
            );

            $this->add_control(
                'filter_color_active',
                [
                    'label' => __('Text Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item.filterbox__item--active' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_background_active',
                [
                    'label' => __('Background Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#333333',
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item.filterbox__item--active' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_border_color_active',
                [
                    'label' => __('Border Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'filter_border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .filterbox__item.filterbox__item--active' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            // Gallery Items Style
            $this->add_control(
                'gallery_items_heading',
                [
                    'label' => __('Gallery Items', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'border_color',
                [
                    'label' => __('Border Color', 'dynamic-gallery'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#F1F0E6',
                    'selectors' => [
                        '{{WRAPPER}} .boxxy-large__item::before' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_section();
        } catch (Throwable $e) {
            error_log('Dynamic_Gallery_Widget register_controls error: ' . $e->getMessage());
        }
    }

    protected function render() {
        try {
            $settings = $this->get_settings_for_display();

            // Debug output for troubleshooting
            error_log('Dynamic Gallery Widget - gallery_items: ' . print_r($settings['gallery_items'], true));
            error_log('Dynamic Gallery Widget - gallery_categories: ' . print_r($settings['gallery_categories'], true));

            // Get available categories
            $available_categories = [];
            foreach ($settings['gallery_categories'] as $category) {
                if (!empty($category['category_name']) && $category['category_name'] !== 'All') {
                    $cat_key = 'cat-' . $category['category_name'];
                    $available_categories[$cat_key] = $category['category_name'];
                }
            }
            // Add 'All' as the first filter
            $all_label = __('All', 'dynamic-gallery');
            $categories_for_filter = array_merge(['cat-all' => $all_label], $available_categories);
            ?>
            <div class="dynamic-gallery-ramble">
                <div class="filterbox">
                    <ul class="ul-reset">
                        <?php $i = 0; foreach ($categories_for_filter as $cat_key => $cat_name): ?>
                            <li class="filterbox__item<?php if ($i === 0) echo ' filterbox__item--active'; ?>" data-filter="<?php echo esc_attr($cat_key); ?>">
                                <span><h3><?php echo esc_html($cat_name); ?></h3></span>
                            </li>
                        <?php $i++; endforeach; ?>
                    </ul>
                </div>
                <div class="boxxy-large filterbox-isotope">
                    <?php foreach ($settings['gallery_items'] as $item):
                        // Always add 'cat-all' to the class list
                        $category = !empty($item['category']) ? $item['category'] : 'cat-all';
                        $item_classes = 'cat-all';
                        if (!empty($category) && $category !== 'cat-all') {
                            $item_classes .= ' ' . esc_attr($category);
                        }
                        $desktop_img = !empty($item['desktop_image']['url']) ? $item['desktop_image']['url'] : (!empty($item['image']['url']) ? $item['image']['url'] : '');
                        $mobile_img = !empty($item['mobile_image']['url']) ? $item['mobile_image']['url'] : (!empty($item['image']['url']) ? $item['image']['url'] : '');
                        $title = $item['title'] ?? '';
                        $url = $item['url']['url'] ?? '';
                        $is_external = !empty($item['url']['is_external']);
                        $nofollow = !empty($item['url']['nofollow']);
                    ?>
                    <div class="boxxy-large__item filterbox-isotope__item <?php echo $item_classes; ?>">
                        <div class="boxxy-large__imgbox">
                            <?php if (!empty($url)): ?>
                                <a href="<?php echo esc_url($url); ?>" class="dropanchor"<?php if ($is_external) echo ' target=\"_blank\"'; if ($nofollow) echo ' rel=\"nofollow\"'; ?>></a>
                            <?php endif; ?>
                            <?php if ($desktop_img): ?>
                            <div class="boxxy-large__imgbox-bg desktop" style="background-image: url('<?php echo esc_url($desktop_img); ?>');">
                                <img src="<?php echo esc_url($desktop_img); ?>" alt="<?php echo esc_attr($title); ?>">
                            </div>
                            <?php endif; ?>
                            <?php if ($mobile_img): ?>
                            <div class="boxxy-large__imgbox-bg mobile" style="background-image: url('<?php echo esc_url($mobile_img); ?>');">
                                <img src="<?php echo esc_url($mobile_img); ?>" alt="<?php echo esc_attr($title); ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <fieldset class="boxxy-large__titlebox">
                            <legend class="boxxy-large__title boxxy-large__title--width-calc">
                                <h4><?php echo esc_html($title); ?></h4>
                            </legend>
                        </fieldset>
                        <?php if (!empty($category) && $category !== 'cat-all' && isset($available_categories[$category])): ?>
                            <div class="gallery-item-category-label">
                                <?php echo esc_html($available_categories[$category]); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
            // Enqueue styles and scripts
            wp_enqueue_style('dynamic-gallery-ramble-style', plugins_url('../assets/css/gallery-style.css', __FILE__));
            wp_enqueue_script('isotope', 'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js', ['jquery'], null, true);
            wp_enqueue_script('dynamic-gallery-ramble-filters', plugins_url('../assets/js/gallery-filters.js', __FILE__), ['jquery', 'isotope'], '1.0.0', true);
        } catch (Throwable $e) {
            error_log('Dynamic_Gallery_Widget render error: ' . $e->getMessage());
        }
    }

    private function get_category_name($category_key, $settings) {
        if (!empty($settings['gallery_categories'])) {
            foreach ($settings['gallery_categories'] as $category) {
                if (!empty($category['category_name']) && sanitize_title($category['category_name']) === $category_key) {
                    return $category['category_name'];
                }
            }
        }
        return $category_key;
    }

    private function get_category_options($settings) {
        $options = [
            'all' => __('All', 'dynamic-gallery')
        ];
        
        if (!empty($settings['gallery_categories'])) {
            foreach ($settings['gallery_categories'] as $category) {
                if (!empty($category['category_name']) && $category['category_name'] !== 'All') {
                    $cat_key = 'cat-' . $category['category_name'];
                    $options[$cat_key] = $category['category_name'];
                }
            }
        }
        return $options;
    }

    public function on_import($settings) {
        return $settings;
    }

    protected function content_template() {
        ?>
        <# 
        var categories = {};
        if (settings.gallery_categories) {
            _.each(settings.gallery_categories, function(cat) {
                if (cat.category_name && cat.category_name !== 'All') {
                    categories[cat.category_name.toLowerCase().replace(/[^a-z0-9]+/g, '-')] = cat.category_name;
                }
            });
        }
        #>
        <div class="dynamic-gallery" data-categories='<?php echo json_encode($this->get_category_options($settings)); ?>'>
            <# if (settings.show_filters === 'yes' && settings.gallery_categories.length) { #>
            <div class="gallery-filters">
                <button class="filter-button active" data-filter="*"><?php echo __('All', 'dynamic-gallery'); ?></button>
                <# _.each(settings.gallery_categories, function(category) { 
                    if (category.category_name && category.category_name !== 'All') {
                        var catKey = category.category_name.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                    #>
                    <button class="filter-button" data-filter=".{{ catKey }}">
                        {{ category.category_name }}
                    </button>
                    <# }
                }); #>
            </div>
            <# } #>

            <div class="gallery-grid">
                <# _.each(settings.gallery_items, function(item) { 
                    var categoryClass = item.category && item.category !== 'none' ? item.category : '';
                #>
                    <div class="gallery-item {{ categoryClass }}">
                        <# if (item.url && item.url.url) { #>
                        <a href="{{ item.url.url }}" 
                           <# if (item.url.is_external) { #>target="_blank"<# } #>
                           <# if (item.url.nofollow) { #>rel="nofollow"<# } #>>
                        <# } #>
                            <div class="gallery-item-image">
                                <img src="{{ item.image.url }}" alt="{{ item.title }}">
                                <div class="gallery-item-overlay"></div>
                                <# if (settings.hover_effect === 'frame' && settings.show_item_name === 'yes') { #>
                                    <div class="gallery-item-name">{{ item.title }}</div>
                                <# } #>
                            </div>
                            <div class="gallery-item-content">
                                <h3>{{ item.title }}</h3>
                                <# if (item.category && item.category !== 'none') { #>
                                    <span class="gallery-item-category">{{ categories[item.category] }}</span>
                                <# } #>
                            </div>
                        <# if (item.url && item.url.url) { #>
                        </a>
                        <# } #>
                    </div>
                <# }); #>
            </div>
        </div>
        <?php
    }
}
