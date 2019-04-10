<?php
/*
Plugin Name: DanzerPress CYA Feeds
Description: CYA WordPress Integration
Plugin URI:  https://danzerpress.com
Author:      Britt Danzer
Version:     1.0
Text Domain: IYC
*/

/**
 * Define Plugin Constants
 */
define('IYC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('IYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once IYC_PLUGIN_DIR . '/vendor/autoload.php';
require_once IYC_PLUGIN_DIR . 'includes/lib/core-functions.php';

new IYC\IYC;

/**
 * TODO:
 *  set locations on plugin activation in database from CYA feed
 *  need to set custom locations that work with cya
 *  need to be able to remove cya locations
 */
$cya_locations = IYC\API::get_xml_locations();
$formatted_shitty_cya_feed_locations = format_shitty_cya_feed_locations();

/**
 * Opened an issue regarding this: 
 * https://github.com/timber/timber/issues/1966
 * This filter helps force first array value to be set in post_custom
 * 
 * created: April 6, 2019
 * updated: TBD
 * 
 * @param array  $customs All post meta array
 * @return array $customs
 */
add_filter('timber_post_get_meta', function($customs) {
    foreach ( $customs as $key => $value ) {
        if ( is_array($value) && isset($value[0]) ) {
            $value = $value[0];
        }
        $customs[$key] = $value;
    }

    return $customs;
});

/**
 * Metabox for yachtlocations
 */
$args = [
    'id' => 'ylocations',
    'title' => 'Yacht Locations',
    'screen' => 'yacht_feed',
    'display' => 'side',
];

$context = [
    'destinations' => set_locations()
];

dp_add_metabox($args, $context);

/**
 * Handles data sanatization for the meta box.
 * 
 * created: April 8, 2019
 * updated: TBD
 *
 * @param array   $data    $_POST array
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return data   $data
 */
add_filter('sanatize_metabox_ylocations', function($data, $post_id, $post) {
    foreach($data as $key => $value) {
        $locations[] = sanitize_text_field($key);
    }
    return $locations;
}, 10, 3);