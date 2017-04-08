<?php
/*
* Plugin Name: Like Ranker
* Author: Fatih Intekin
* Description: Creates a rating system for your posts. Allows visitors and users to "like" your posts, and provides a widget where you can list your top 10 liked posts. Also creates a list in an admin panel, where you can see the like counts of each tag.
* Version:     1.0.0
* Author URI:  https://github.com/fatihint
* License:     GPLv3
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if file is accessed directly
if ( !defined('ABSPATH') ) {
    exit;
}

require_once ( plugin_dir_path(__FILE__) . 'like-post-meta.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-display-content.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-user-request.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-ranker-plugin-page.php' );
require_once ( plugin_dir_path(__FILE__) . 'classes/like-ranker-widget.php' );


// Enqueue scripts for admin panel
function lr_enqueue_admin_scripts() {
	global $pagenow;
    if( $pagenow == 'admin.php' ) {
        wp_enqueue_style( 'likeranker-admin-css', plugins_url( 'css/admin-page.css', __FILE__ ) );
    }
}

// Enqueue scripts for theme
function lr_enqueue_ui_scripts() {
    wp_enqueue_script( 'core-likeranker-js', plugins_url( 'js/like.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), false, true );
    wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'likeranker-ui-css', plugins_url( 'css/like-bar.css', __FILE__ ) );
    wp_enqueue_style( 'likeranker-widget-css', plugins_url( 'css/widget.css', __FILE__ ) );
    wp_localize_script( 'core-likeranker-js', 'my_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('user-like') ) );
}
add_action( 'wp_enqueue_scripts', 'lr_enqueue_ui_scripts');
add_action( 'admin_enqueue_scripts', 'lr_enqueue_admin_scripts' );

// Register and load the widget
function lr_load_widget() {
   register_widget( 'lr_widget' );
}
add_action( 'widgets_init', 'lr_load_widget' );
