<?php
/*
* Plugin Name: Like Ranker
* Plugin URI: http://likeranker.github.io
* Author: Fatih Intekin
* Description: Rank your posts according to their like counts.
* Version:     0.0.1
* Author URI:  https://developer.wordpress.org/
* License:     GPLv2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: wporg
* Domain Path: /languages
*/

// Exit if file is accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once ( plugin_dir_path(__FILE__) . 'like-post-meta.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-display-content.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-user-request.php' );
require_once ( plugin_dir_path(__FILE__) . 'like-ranker-plugin-page.php' );
require_once ( plugin_dir_path(__FILE__) . 'classes/like-ranker-widget.php' );


function lr_enqueue_scripts() {

	global $pagenow, $typenow;

    wp_enqueue_script( 'core-likeranker-js', plugins_url( 'js/like.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), false, true );
    wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

    if($pagenow == 'admin.php?page=like_ranker') {
        wp_enqueue_style( 'likeranker-admin-css', plugins_url( 'css/admin-page.css', __FILE__ ) );
    }
    wp_localize_script( 'core-likeranker-js', 'my_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('user-like') ) );
}

add_action( 'wp_enqueue_scripts', 'lr_enqueue_scripts' );

// Register and load the widget
function lr_load_widget() {
   register_widget( 'lr_widget' );
}
add_action( 'widgets_init', 'lr_load_widget' );
