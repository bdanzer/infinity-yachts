<?php
/*
Plugin Name: DanzerPress CYA Feeds
Description: Example demonstrating how to use the HTTP API to make a GET request.
Plugin URI:  https://plugin-planet.com/
Author:      Jeff Starr
Version:     1.0
*/

// include plugin dependencies: admin only
if ( is_admin() ) {

	require_once plugin_dir_path( __FILE__ ) . 'admin/admin-ajax.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-register.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-callbacks.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-validate.php';
	
}



// include plugin dependencies: admin and public
require_once plugin_dir_path( __FILE__ ) . 'admin/class/settings-page-class.php';
require_once plugin_dir_path( __FILE__ ) . 'templates/template-function.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/includes/core-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/wp-cron/wp-cron-scheduler.php';
require_once plugin_dir_path( __FILE__ ) . 'public/public-ajax.php';
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');



function load_custom_wp_admin_style($hook) {
        // Load only on ?page=mypluginname
        if($hook != 'toplevel_page_danzerpress') {
                return;
        }
        wp_enqueue_style( 'custom_wp_admin_css', plugins_url('/admin/css/danzerpress-admin.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );



/**
* The below function will help to load template file from plugin directory of wordpress
*  Extracted from : http://wordpress.stackexchange.com/questions/94343/get-template-part-from-plugin
*/ 
define('PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
function ccm_get_template_part($slug, $name = null) {
    do_action("ccm_get_template_part_{$slug}", $slug, $name);
    $templates = array();
    if (isset($name))
        $templates[] = "{$slug}-{$name}.php";
        $templates[] = "{$slug}.php";
    ccm_get_template_path($templates, true, false);
}



/* Extend locate_template from WP Core 
* Define a location of your plugin file dir to a constant in this case = PLUGIN_DIR_PATH 
* Note: PLUGIN_DIR_PATH - can be any folder/subdirectory within your plugin files 
*/ 
function ccm_get_template_path($template_names, $load = false, $require_once = true ) {
    $located = ''; 
    foreach ( (array) $template_names as $template_name ) { 
      if ( !$template_name ) 
        continue; 
      /* search file within the PLUGIN_DIR_PATH only */ 
      if ( file_exists(PLUGIN_DIR_PATH . $template_name)) { 
        $located = PLUGIN_DIR_PATH . $template_name; 
        break; 
      } 
    }
    if ( $load && '' != $located )
        load_template( $located, $require_once );
    return $located;
}



function yacht_setup_post_type() {
    $args = array(
        'public'    => true,
        'label'     => __( 'Yacht Feed', 'textdomain' ),
        'menu_icon' => 'dashicons-book',
        'has_archive' => false,
        'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
        'rewrite' => array(
            'slug' => 'yacht',
        ),
    );
    register_post_type( 'yacht_feed', $args );
}
add_action( 'init', 'yacht_setup_post_type' );




function review_setup_post_type() {
    $args = array(
        'public'    => true,
        'label'     => __( 'Reviews', 'textdomain' ),
        'menu_icon' => 'dashicons-book',
        'has_archive' => 'reviews',
        'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
        'rewrite' => array(
            'slug' => 'review',
        ),
    );
    register_post_type( 'review_feed', $args );
}
add_action( 'init', 'review_setup_post_type' );