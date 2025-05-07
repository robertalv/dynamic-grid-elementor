<?php
/*
Plugin Name: Socially Up Dynamic Filter Gallery
Description: Adds a dynamic filterable gallery widget to Elementor.
Version: 1.0.1
Author: Socially Up
Author URI: https://sociallyup.com
Text Domain: dynamic-filter-gallery
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'DFG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'DFG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

defined( 'DFG_VERSION' ) || define( 'DFG_VERSION', '1.0.0' );

// Load plugin after Elementor is loaded.
add_action( 'plugins_loaded', 'dfg_init_plugin' );
add_action( 'wp_enqueue_scripts', 'dfg_register_gallery_assets' );
add_action( 'elementor/frontend/after_register_scripts', 'dfg_register_gallery_assets' );
add_action( 'elementor/frontend/after_register_styles', 'dfg_register_gallery_assets' );

function dfg_init_plugin() {
    // Check if Elementor is active.
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'dfg_elementor_missing_notice' );
        return;
    }
    // Register widget after Elementor is loaded.
    add_action( 'elementor/widgets/register', 'dfg_register_filterable_gallery_widget' );
}

function dfg_register_filterable_gallery_widget( $widgets_manager ) {
    require_once DFG_PLUGIN_PATH . 'includes/class-dfg-elementor-widget.php';
    $widgets_manager->register( new DFG_Filterable_Gallery_Widget() );
}

function dfg_elementor_missing_notice() {
    echo '<div class="notice notice-warning is-dismissible"><p>';
    echo esc_html__( 'Dynamic Filter Gallery for Elementor requires Elementor to be installed and activated.', 'dynamic-filter-gallery' );
    echo '</p></div>';
}

function dfg_register_gallery_assets() {
    wp_register_script(
        'dfg-filterable-gallery-js',
        DFG_PLUGIN_URL . 'assets/js/dynamic-filter-gallery.js',
        [ 'jquery' ],
        DFG_VERSION,
        true
    );
    wp_register_style(
        'dfg-filterable-gallery-css',
        DFG_PLUGIN_URL . 'assets/css/dynamic-filter-gallery.css',
        [],
        DFG_VERSION
    );
}

// Disable auto updates for this plugin
add_filter( 'auto_update_plugin', function( $update, $item ) {
    if ( isset( $item->slug ) && $item->slug === 'dynamic-filter-gallery' ) {
        return false;
    }
    return $update;
}, 10, 2 ); 