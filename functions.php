<?php
/*
Plugin Name: DanzerPress CYA Feeds
Description: CYA WordPress Integration
Plugin URI:  https://danzerpress.com
Author:      Britt Danzer
Version:     1.0
*/

/**
 * Define dir Constants
 */
define('IYC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('IYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once IYC_PLUGIN_DIR . '/vendor/autoload.php';
require_once IYC_PLUGIN_DIR . 'includes/lib/core-functions.php';

new IYC\IYC;

// add_action('init', function() {
//     $args = array(
//         'post_type'  => 'yacht_feed',
//         'meta_query' => array(
//             array(
//                 'key'     => 'boat_type',
//                 'value'   => 'Power',
//             ),
//             array(
//                 'key'     => 'cruise_speed',
//                 'value'   => 12,
//             ),
//         ),
//     );
//     $query = new WP_Query($args);
//     var_dump($query);
// die;
// });


// $locations = IYC\API::get_xml_locations_array();
// var_dump($locations);
// die;