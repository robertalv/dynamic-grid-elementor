<?php
/*
Plugin Name: Dynamic Gallery for Elementor
Plugin URI: https://devwithbobby.com
Description: Adds a dynamic gallery widget to Elementor
Version: 1.0.9
Author: Bobby Alv
*/

if (!defined('ABSPATH')) exit;

// Register Widget
function register_dynamic_gallery_widget($widgets_manager) {
    try {
        require_once(__DIR__ . '/widgets/dynamic-gallery.php');
        $widgets_manager->register(new \Dynamic_Gallery_Widget());
    } catch (Throwable $e) {
        error_log('Dynamic Gallery Widget registration error: ' . $e->getMessage());
    }
}
add_action('elementor/widgets/register', 'register_dynamic_gallery_widget');

// Enqueue scripts and styles
function dynamic_gallery_scripts() {
    // Enqueue imagesLoaded
    wp_enqueue_script(
        'imagesloaded',
        'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js',
        array('jquery'),
        '5.0.0',
        true
    );

    // Enqueue Isotope
    wp_enqueue_script(
        'isotope',
        'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js',
        array('jquery', 'imagesloaded'),
        '3.0.6',
        true
    );

    // Enqueue our custom script
    wp_enqueue_script(
        'dynamic-gallery',
        plugins_url('assets/js/dynamic-gallery.js', __FILE__),
        array('jquery', 'isotope', 'imagesloaded'),
        '1.0.0',
        true
    );

    // Enqueue gallery filter script
    wp_enqueue_script(
        'dynamic-gallery-ramble-filters',
        plugins_url('assets/js/gallery-filters.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    // Enqueue our custom styles
    wp_enqueue_style(
        'dynamic-gallery',
        plugins_url('assets/css/dynamic-gallery.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'dynamic_gallery_scripts');

// Enable auto-updates for this plugin
add_filter('auto_update_plugin', function($update, $item) {
    if (isset($item->plugin) && $item->plugin === plugin_basename(__FILE__)) {
        return true;
    }
    return $update;
}, 10, 2);
