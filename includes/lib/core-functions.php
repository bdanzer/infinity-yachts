<?php //DanzerPress CYA Feeds

/*--------------------------------------------------------------
##General Functions
--------------------------------------------------------------*/
function keep_breaks($value) {
    return nl2br(str_replace(' ', '&nbsp;', $value));
}

function get_iyc_url() {
	return IYC\IYC::get_url();
}

function get_iyc_dir() {
	return IYC\IYC::get_dir();
}

function get_context() {
	return IYC\Context::get_context();
}

function render($template, $context) {
	Timber\Timber::render($template, $context);
}

function dp_add_metabox($args, $context = []) {
	$metabox = new IYC\metabox\Metabox($args, $context);
}

function component_render($component, array $context = []) {
	$components = new IYC\components\Components();
	$components->$component($context);
}

/**
 * Helps filter yacht feed search
 * 
 * created: 4/6/2019
 * updated: TBA
 * 
 * @param string  $sql      Post ID
 * @param class   $wp_query WP_Query object
 * 
 * @return string $sql Sql string
 */
function yacht_feed_search($sql, $wp_query) {
	$yacht_name = sanitize_text_field($_POST['yachtName']);

	if ($wp_query->get('post_type') === 'yacht_feed') {
		$sql = "AND `post_title` LIKE '%{$yacht_name}%'" . $sql;
	}
	return $sql;
}

/**
 * Helps get yacht id
 * 
 * created: 4/9/2019
 * updated: TBA
 * 
 * @param int $post_id Post ID
 * 
 * @return int $post_meta Yacht ID
 */
function get_yacht_id($post_id = null) {
	if (!$post_id) {
		$post_id = (get_the_ID()) ?: null;
	}

	return intval(get_post_meta($post_id, 'yacht_id', true));
}

/**
 * Helps get post id from yacht id in postmeta table
 * 
 * created: 4/9/2019
 * updated: TBA
 * 
 * @param int $yacht_id Yacht ID
 * 
 * @return int $post_id Post ID
 */
function get_post_id_from_yacht_id($yacht_id = null) {
	global $wpdb;
	$sql = "SELECT `post_id` FROM `wp_postmeta`
		WHERE `meta_key` = 'yacht_id' 
		AND `meta_value` = %d
	";

	$query = $wpdb->prepare($sql, $yacht_id);

	$post_id = $wpdb->get_col($query);

	if (empty($post_id)) {
		return false;
	}

	return intval($post_id[0]);
}

/**
 * Helps get post id from destination key in postmeta table
 * 
 * created: 6/15/2019
 * updated: TBA
 * 
 * @param  int $destination_key Destination Key
 * 
 * @return int $post_id         Post ID
 */
function get_post_id_from_dest_key($destination_key = null) {
	global $wpdb;
	$sql = "SELECT `post_id` FROM `wp_postmeta`
		WHERE `meta_key` = 'IYC_destination_key' 
		AND `meta_value` = %s
	";

	$query = $wpdb->prepare($sql, $destination_key);

	$post_id = $wpdb->get_col($query);

	if (empty($post_id)) {
		return false;
	}

	return intval($post_id[0]);
}

/**
 * Helps get yacht meta for searches from $_POST
 * 
 * created: 4/8/2019
 * updated: TBA
 * 
 * @param $form_post $_POST data or array
 * 	'price'
 * 	'yachtLen
 * 	'ylocations'
 * 	'boatType'
 * 	'staterooms'
 * 	'guests'
 * 
 * @return array $meta_query WP_Query meta_query arg
 */
function yacht_meta_query($form_post = null) {
		
	$defaults = [
		'ylocations' => null,
		'boatType' => null, 
		'staterooms' => null, 
		'guests' => null,
		'price' => 0,
		'yachtLen' => 0
	];

	$form_post = wp_parse_args($form_post, $defaults);

	$price_arr = IYC\helpers\YachtHelper::get_yacht_price($form_post['price']);
	$length_arr = IYC\helpers\YachtHelper::get_yacht_length($form_post['yachtLen']);

	$boat_meta = [
		'dp_metabox_ylocations' => sanitize_text_field($form_post['ylocations']),
		'boat_type' => sanitize_text_field($form_post['boatType']), 
		'staterooms' => intval($form_post['staterooms']), 
		'guests' => intval($form_post['guests']),
		'price_from' => [
			'from' => $price_arr['price_from'],
			'to' => $price_arr['price_to']
		],
		'length_feet' => [
			'from' => $length_arr['length_from'],
			'to' => $length_arr['length_to']
		]
	];

	$meta_query = [];

	/**
	 * Setting up meta_query
	 * Not the cleanest but seems most DRY
	 */
	foreach ($boat_meta as $variable => $value) {
		if (isset($value['to']) && $value['to'] === 'all' || $value === 'all')
			continue;
		
		if (empty($value))
			continue;

		/**
		 * For now handles the price_from/length_from logic
		 */
		if (is_array($value)) {
			$meta_query[$variable] = [
				'key'     => $variable,
				'value'   => [$value['from'], $value['to']],
				'compare' => 'BETWEEN',
				'type'    => 'numeric',
			];
		} elseif ($variable === 'dp_metabox_ylocations') {
			$meta_query[$variable] = [
				'key' => $variable,
				'value' => serialize($value),
				'compare' => 'LIKE'
			];
		} else {
			$meta_query[$variable] = [
				'key'     => $variable,
				'value'   => $value,
			];
		}
	}

	return $meta_query;
}


/*--------------------------------------------------------------
##Yacht Locations
--------------------------------------------------------------*/

/**
 * TODO: Should not be basing post on src{$num}
 * Should change to using post meta like the yacht ids
 */
//add_action( 'admin_init', 'create_location_pages' );
function create_location_page($location_key, $post_title) {
	$post_id = get_post_id_from_dest_key($location_key);

	if ( FALSE === get_post_status( $post_id ) ) {
		$args = array(
			'post_title'     => $post_title,
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id(),
			// Assign page template
			'page_template'  => 'destination-template.php'
		);

		$new_page_id = wp_insert_post($args);

		if ( $new_page_id && ! is_wp_error( $new_page_id ) ){
			update_post_meta( $new_page_id, '_wp_page_template', 'destination-individual.php' );
			update_post_meta( $new_page_id, 'IYC_destination_key', $location_key );
		}
	} else {
		// The post exists
	}

	return $new_page_id;
}


/*--------------------------------------------------------------
##CYA Specifics
--------------------------------------------------------------*/
function cya_feed_content($yacht_id) {
	//Convert to XML Object
	$xml_ebrochure = IYC\API::get_xml_ebrochure($yacht_id);

	if (strpos($xml_ebrochure['yachtLowPrice'], '&#36;') !== FALSE) {
		$currency = '&#36;';
		$currency_type = 'USD';
	} else {
		$currency = '&#8364;';
		$currency_type = 'EURO';
	}

	//Creating feed content array
	$cya_feed_content = array(
		'name' => ucfirst(strtolower($xml_ebrochure['yachtName'])),
		'content' => $xml_ebrochure['yachtAccommodations'],
		'guests' => $xml_ebrochure['yachtPax'],
		'staterooms' => $xml_ebrochure['yachtCabins'],
		'length_feet' => $xml_ebrochure['sizeFeet'],
		'length_meters' => $xml_ebrochure['sizeMeter'],
		'beam' => $xml_ebrochure['yachtBeam'],
		'draft' => $xml_ebrochure['yachtDraft'],
		'built' => $xml_ebrochure['yachtYearBuilt'],
		'refit' => $xml_ebrochure['yachtRefit'],
		'builder' => $xml_ebrochure['yachtBuilder'],
		'cruise_speed' => strtolower($xml_ebrochure['yachtCruiseSpeed']),
		'cruise_max_speed' => strtolower($xml_ebrochure['yachtMaxSpeed']),
		'currency' => $currency_type,
		'price_from' => filter_var(str_replace($currency, '', $xml_ebrochure['yachtLowPrice']), FILTER_SANITIZE_NUMBER_INT),
		'price_to' => filter_var(str_replace($currency, '', $xml_ebrochure['yachtHighPrice']), FILTER_SANITIZE_NUMBER_INT),
		'boat_type' => $xml_ebrochure['yachtType'],
		'locations_added' => IYC\helpers\YachtHelper::get_location_codes($xml_ebrochure['yachtSummerArea'], $xml_ebrochure['yachtWinterArea']),
		'image' => $xml_ebrochure['yachtPic1'],
		'desc' => $xml_ebrochure['yachtDesc1'],
		'dingy' => $xml_ebrochure['yachtDinghy'],
		'dingy_hp' => $xml_ebrochure['yachtDinghyHp'],
		'paddle' => $xml_ebrochure['yachtStandUpPaddle'],
		'single_kayak' => $xml_ebrochure['yacht1ManKayak'],
		'double_kayak' => $xml_ebrochure['yacht2ManKayak'],
		'adult_water_skis' => $xml_ebrochure['yachtAdultWSkis'],
		'kid_water_skis' => $xml_ebrochure['yachtKidsSkis'],
		'wakeboard' => $xml_ebrochure['yachtWakeBoard'],
		'kneeboard' => $xml_ebrochure['yachtKneeBoard'],
		'wave_runner' => $xml_ebrochure['yachtWaveRun'],
		'jet_skis' => $xml_ebrochure['yachtJetSkis'],
		'snorkel' => $xml_ebrochure['yachtGearSnorkel'],
		'tube' => $xml_ebrochure['yachtTube'],
		'fishing_gear' => $xml_ebrochure['yachtFishingGear'],
		'scuba_diving' => $xml_ebrochure['yachtScubaOnboard'],
		'air_compressor' => $xml_ebrochure['yachtCompressor'], 
	);

	return $cya_feed_content;
}

function cya_acf_fields($cya_feed_content = NULL, $yacht_id = NULL) {

	if ($cya_feed_content === NULL) {
		$cya_feed_content = cya_feed_content($yacht_id);
	}

	$meta_array = [
		'acf_fields' => [
			array($cya_feed_content['content'], 'field_5a69b88d45254', 'boat_description'),
			array($cya_feed_content['guests'], 'field_5a9371571b4de', 'guests'),
			array($cya_feed_content['staterooms'], 'field_5a93719456d2a', 'staterooms'),
			array($cya_feed_content['length_feet'], 'field_5a9371a356d2b', 'length_feet'),
			array($cya_feed_content['length_meters'], 'field_5a9371b556d2c', 'length_meters'),
			array($cya_feed_content['beam'], 'field_5a9371bc56d2d', 'beam'),
			array($cya_feed_content['draft'], 'field_5a9371c856d2e', 'draft'),
			array($cya_feed_content['built'], 'field_5a9371d556d2f', 'built'),
			array($cya_feed_content['refit'], 'field_5a9371d856d30', 'refit'),
			array($cya_feed_content['builder'], 'field_5a9371e156d31', 'builder'),
			array($cya_feed_content['cruise_speed'], 'field_5a9371ee56d32', 'cruise_speed'),
			array($cya_feed_content['cruise_max_speed'], 'field_5a9371f956d33', 'cruise_max_speed'),
			array($cya_feed_content['price_from'], 'field_5a94e7c214579', 'price_from'),
			array($cya_feed_content['price_to'], 'field_5a94e7dd1457a', 'price_to'),
			array($cya_feed_content['boat_type'], 'field_5a97a01bb5b0b', 'boat_type'),
			array($cya_feed_content['dingy'], 'field_5a9ba95d49e5f', 'dingy'),
			array($cya_feed_content['dingy_hp'], 'field_5a9ba990786d5', 'dingy_hp'),
			array($cya_feed_content['paddle'], 'field_5a9ba95d49e73', 'paddle'),
			array($cya_feed_content['single_kayak'], 'field_5a9ba9a4786d6', 'single_kayak'),
			array($cya_feed_content['double_kayak'], 'field_5a9ba9c2786d7', 'double_kayak'),
			array($cya_feed_content['adult_water_skis'], 'field_5a9ba9d2786d8', 'adult_water_skis'),
			array($cya_feed_content['kid_water_skis'], 'field_5a9ba9dc786d9', 'kid_water_skis'),
			array($cya_feed_content['wakeboard'], 'field_5a9ba9e6786da', 'wakeboard'),
			array($cya_feed_content['kneeboard'], 'field_5a9ba9f3786db', 'kneeboard'),
			array($cya_feed_content['wave_runner'], 'field_5a9baa01786dc', 'wave_runner'),
			array($cya_feed_content['jet_skis'], 'field_5a9baa14786dd', 'jet_skis'),
			array($cya_feed_content['snorkel'], 'field_5a9baa1f786de', 'snorkel'),
			array($cya_feed_content['tube'], 'field_5a9baa2b786df', 'tube'),
			array($cya_feed_content['fishing_gear'], 'field_5a9baa30786e0', 'fishing_gear'),
			array($cya_feed_content['scuba_diving'], 'field_5aade0882f5bf', 'scuba_diving'),
			array($cya_feed_content['air_compressor'], 'field_5aade0bd2f5c0', 'scuba_compressor')
		],
		'post_meta' => [
			[$cya_feed_content['locations_added'], 'dp_metabox_ylocations', 'locations_added']
		]
	];

	return $meta_array;
}


/*--------------------------------------------------------------
##Check for updates
--------------------------------------------------------------*/

/**
 * Loop through and Update all ACF fields or post meta
 * 
 * Created: 4/8/2019
 * Updated: TBA
 * 
 * @param meta: array with acf_fields or post_meta basically switching from acf vs wordpress meta
 * @param post_id: post id passed
 */
function iyc_update_meta($meta_array, $post_id) {
	foreach ($meta_array as $meta_type => $meta_type_array) {
		foreach ($meta_type_array as $value_array) {
			$value = $value_array[0];
			$key = $value_array[1];

			if ($meta_type === 'acf_fields') {
				update_field($key, $value, $post_id);
			} elseif ($meta_type === 'post_meta') {
				update_post_meta($post_id, $key, $value);
			}
		}
	}
}

/**
 * Check for similarties of new vs old content
 * 
 * created: 2/20/2018
 * updated: TBA
 * 
 * @param string  $content_new new content
 * @param string  $content_old old content
 * @param int	  $field_id    acf field id
 * @param int 	  $post_id 	   post id
 * @param WP_Post $my_post	   WordPress post object
 **/
function check_similarities($content_new, $content_old, $field_id = null, $post_id = null, $my_post = null) {
	$content_stripped_1 = strip_tags(trim((string)$content_new));
	$content_stripped_1 = preg_replace('/\s+/', '', $content_stripped_1);

	$content_stripped_2 = strip_tags(trim((string)$content_old));
	$content_stripped_2 = preg_replace('/\s+/', '', $content_stripped_2);

	if ($content_stripped_1 === $content_stripped_2) {
		//Do Nothing
	} else {

		if ($field_id !== FALSE) {
			
			$update_yacht = [
				'yacht_id' => '',
				'content_old' => '',
				'content_new' => '',
			];

			$update_yacht['yacht_id'] = $post_id;
			$update_yacht['content_old'] = $content_old;
			$update_yacht['content_new'] = $content_new;

			$database = get_option('yacht_changes');

			if ($database == '') {
				//Update yacht change in database
				update_option( 'yacht_changes', $update_yacht );
			} else {
				$database_array = array_merge($update_yacht, $database);
				update_option( 'yacht_changes', $database_array );
			}

			update_field( $field_id, $content_new, $post_id );
		} 

		// Update the post into the database
		if ($my_post) {
			wp_update_post( $my_post );
		}
	}
	
}

/*Check for xml updates from the feed*/
function check_for_xml_updates() {
	//get all yacht url
	//$xml = get_xml_snapin_url();
	$xml = IYC\API::get_xml_snyachts_url();

	//convert into simple object
	$xml_snapins = simplexml_load_file($xml);

	//get current boats in database
	$option_value = get_option('danzerpress_options');

	//Loop through each boat form xml snapins
	foreach ($xml_snapins as $value) {

		$post_id = (int)$value->yachtId;

		//Check if boat from feed exists in database
		if(in_array($value->yachtId, $option_value) && get_post_status($post_id) != 'trash' && (int)get_field('overide_cya', $post_id) != 1) {

			//Set yacht_id
			$yacht_id = (int)$value->yachtId;

			//get cya feed content
			$cya_feed_content = cya_feed_content($yacht_id);

			//Set post array
			$post_arr = array(
				'ID' => $yacht_id,
			    'post_title'   => $cya_feed_content['name'],
			);

			//get image id
			$image_id = get_post_thumbnail_id($yacht_id);

			//delete current image
			wp_delete_attachment($image_id, true);

			//upload new image
			$media_id = media_sideload_image($cya_feed_content['image'], $yacht_id, $cya_feed_content['desc'], 'id');

			//set new picture to post
			set_post_thumbnail($yacht_id, $media_id);

			//get fields
			$acf_fields = cya_acf_fields($cya_feed_content);

			//Update acf fields
			foreach ($acf_fields as $acf_array) {
			    $content = $acf_array[0];
			    $field_key = $acf_array[1];
			    $field_name = $acf_array[2];

				//check_similarities($content_new, $content_old, $field_id = null, $post_id = null, $my_post = null)
				check_similarities($content, get_field($field_name, $yacht_id), $field_key, $yacht_id);
			}	

		} else {
			if ( get_post_status ( $post_id ) ) {
				
			}
		}
	}

}