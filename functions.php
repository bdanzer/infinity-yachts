<?php
/*
Plugin Name: DanzerPress CYA Feeds
Description: CYA WordPress Integration
Plugin URI:  https://danzerpress.com
Author:      Britt Danzer
Version:     2.0
Text Domain: IYC
*/

/**
 * Define Plugin Constants
 */
define('IYC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('IYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once IYC_PLUGIN_DIR . '/vendor/autoload.php';
require_once IYC_PLUGIN_DIR . 'includes/lib/core-functions.php';

$IYC_Checker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/bdanzer/infinity-yachts',
	__FILE__,
	'infinity-yachts'
);

$IYC_Checker->setAuthentication('5900e7b4b8248adae49d98d364d55b51d29843f4');
$IYC_Checker->setBranch('master');

/**
 * Init Plugin
 */
new IYC\IYC;

/**
 * Tester function to get all sorted yacht ids from cya feed
 * 
 * created: June 15, 2019
 * updated: TBD
 * 
 * @return array $yacht_ids Yacht Ids dumped on to page after die statement
 */
function find_all_cya_feed_yacht_ids() {
    $snap = IYC\API::get_xml_snyachts();

    $yacht_ids = [];
    foreach($snap as $yacht) {
        foreach($yacht as $key => $value) {
            $yacht_ids[] = (int)$value['yachtId'];
        }
    }

    sort($yacht_ids);

    return $yacht_ids;
}

/**
 * Tester function to get all sorted yacht ids from cya feed
 * 
 * created: June 15, 2019
 * updated: TBD
 * 
 * @param int    $yacht_id
 * 
 * @return array $yacht_data Yacht data passed from cya feed
 */
function find_yacht_in_cya_feed($yacht_id) {
    echo '<pre>';
    var_dump(IYC\API::get_xml_ebrochure($yacht_id));
    echo '</pre>';
    die;
}

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
    'destinations' => IYC\helpers\YachtHelper::get_locations()
];

dp_add_metabox($args, $context);

/**
 * Handles data sanatization for the meta box.
 * 
 * created: April 8, 2019
 * updated: April 10, 2019
 *
 * @param array   $data    $_POST array
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return data   $data
 */
function sanitize_ylocations($data, $post_id, $post) {
    $locations = [];

    foreach($data as $key => $value) {
        $locations[] = sanitize_text_field($key);
    }
    return $locations;
}