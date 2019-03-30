<?php
/*
Plugin Name: DanzerPress CYA Feeds
Description: CYA WordPress Integration
Plugin URI:  https://danzerpress.com
Author:      Britt Danzer
Version:     1.0
*/

require_once __DIR__ . '/vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/lib/core-functions.php';

$timber = new Timber\Timber();
$timber::$dirname = 'templates';
$timber::$locations = [
    plugin_dir_path( __FILE__ ) . 'templates'
];

define('IYC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('IYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

// include plugin dependencies: admin only
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
    
    new IYC\Settings;
    //new IYC\WPCron;
}

new IYC\Ajax;
new IYC\IYC;
add_action('plugins_loaded', [IYC\PageTemplater::class, 'get_instance']);

// include plugin dependencies: admin and public
require_once plugin_dir_path( __FILE__ ) . 'public/public-ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/filters.php';
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

/* Filter the single_template with our custom function*/
add_filter('single_template', 'feed_template', 99);
function feed_template($single) {
    global $wp_query, $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'yacht_feed' ) {
        return get_iyc_dir() . 'templates/wp-templates/single-yacht_feed.php';
    }

    return $single;
}
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