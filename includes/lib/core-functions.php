<?php //DanzerPress CYA Feeds

/*--------------------------------------------------------------
##General Functions
--------------------------------------------------------------*/
function build_sorter($key) {
	return function ($a, $b) use ($key) {
	    return strnatcmp($b[$key], $a[$key]);
	};
}

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

function yacht_feed_search($sql, $wp_query) {
	$yacht_name = sanitize_text_field($_POST['yachtName']);

	if ($wp_query->get('post_type') === 'yacht_feed') {
		$sql = "AND `post_title` LIKE '%{$yacht_name}%'" . $sql;
	}
	return $sql;
}

function get_yacht_price($case) {
	$price_arr = [];
	
	switch ($case) {
		case 0:
	    	$price_arr['price_from'] = 'all';
	    	$price_arr['price_to'] = 'all';
	    	break;
	    case 1:
	        $price_arr['price_from'] = 1;
	        $price_arr['price_to'] = 9999;
	        break;
	    case 2:
	        $price_arr['price_from'] = 10000;
	        $price_arr['price_to'] = 19999;
	        break;
	    case 3:
	        $price_arr['price_from'] = 20000;
	        $price_arr['price_to'] = 29999;
	        break;
	    case 4:
	        $price_arr['price_from'] = 30000;
	        $price_arr['price_to'] = 49999;
	        break;
	    case 5:
	        $price_arr['price_from'] = 50000;
	        $price_arr['price_to'] = 100000;
	        break;
	    case 6:
	        $price_arr['price_from'] = 100001;
	        $price_arr['price_to'] = 999999;
	        break;
	}

	return $price_arr;
}

function get_yacht_length($case) {
	$length_arr = [];
	switch ($case) {
		case 0:
			$length_arr['length_from'] = 'all';
			$length_arr['length_to'] = 'all';
			break;
		case 1:
	    	$length_arr['length_from'] = 1;
	    	$length_arr['length_to'] = 49;
	    	break;
	    case 2:
	        $length_arr['length_from'] = 50;
	        $length_arr['length_to'] = 59;
	        break;
	    case 3:
	        $length_arr['length_from'] = 60;
	        $length_arr['length_to'] = 79;
	        break;
	    case 4:
	        $length_arr['length_from'] = 80;
	        $length_arr['length_to'] = 99;
	        break;
	    case 5:
	        $length_arr['length_from'] = 100;
	        $length_arr['length_to'] = 119;
	        break;  
	    case 6:
	        $length_arr['length_from'] = 120;
	        $length_arr['length_to'] = 139;
	        break;
	    case 7:
	        $length_arr['length_from'] = 140;
	        $length_arr['length_to'] = 169;
	        break;
	    case 8:
	        $length_arr['length_from'] = 170;
	        $length_arr['length_to'] = 199;
	        break;
	    case 9:
	        $length_arr['length_from'] = 200;
	        $length_arr['length_to'] = 50000;
	        break;    
	}

	return $length_arr;
}


/*--------------------------------------------------------------
##XML Functions
--------------------------------------------------------------*/
function get_xml_user_id() {
	$user_id = '128';

	return $user_id;
}

function get_xml_domain() {
	$domain = 'infinityyachts.com';

	return $domain;
}

function get_xml_api_code() {
	$apicode = '128Sx%$yerO9s3';

	return $apicode;
}

function get_xml_snapin_url() {
	$user_id = get_xml_user_id();
	$domain = get_xml_domain();
	$apicode = get_xml_api_code();

	$url = 'http://www.centralyachtagent.com/snapins/snyachts-xml.php?user=' . $user_id . '';

	return $url;
}

function get_xml_ebrochure_url($yacht_id = NULL) {
	if ($yacht_id === NULL) {
		$id = get_the_ID();
	} else {
		$id = $yacht_id;
	}

	$user_id = get_xml_user_id();
	$domain = get_xml_domain();
	$apicode = get_xml_api_code();

	$url = 'https://www.centralyachtagent.com/snapins/ebrochure-xml.php?user=' . $user_id . '&idin=' . $id . '&act=' . $domain . '&apicode=' . $apicode . '';

	return $url;
}

function xml_json_decode($yacht_id = NULL) {
    if ($yacht_id !== NULL) {
        $id = $yacht_id;
    } else {
        $id = get_the_ID();
    }

    $url = get_xml_ebrochure_url($id);
    $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

    $array = json_decode(json_encode($xml), true);

    //Removing Blank Arrays or keeping the first array value
    $new_array = array();
    foreach ($array['yacht'] as $key => $value) {
        if (!is_array($value)) {
            $new_array[$key] = $value;
        } elseif (is_array($value) && !empty($value)) {
            $new_array[$key] = $value[0];
        } else {
            $new_array[$key] = '';
        }
    }

    return $new_array;
}



/*--------------------------------------------------------------
##Yacht Locations
--------------------------------------------------------------*/
function set_locations() {
	$specific_locations = array(
		'src13' => 'alaska',
		'src28' => 'antarctica',
		'src29' => 'arctic',
		'src21' => 'australia',
		'src5' => 'bahamas',
		//IYC Location SRC50
		'src50' => 'british colombia',
		'src17' => 'california',
		'src34' => 'canary islands',
		'src7' => 'caribbean leewards',
		'src3' => 'caribbean virgin islands',
		'src8' => 'caribbean windwards',
		'src20' => 'central america',
		'src16' => 'croatia',
		'src32' => 'cuba',
		'src26' => 'dubai',
		'src10' => 'florida',
		'src30' => 'french polynesia',
		'src31' => 'galapagos',
		'src18' => 'great lakes',
		'src4' => 'greece',
		'src12' => 'indian ocean and se asia',
		//IYC Location SRC50
		'src51' => 'indonesia',
		'src19' => 'mexico',
		'src9' => 'new england',
		'src22' => 'new zealand',
		'src24' => 'northern europe',
		'src14' => 'pacific nw',
		'src25' => 'red sea',
		'src2' => 'south america',
		'src33' => 'south china sea',
		'src23' => 'south pacific',
		'src11' => 'turkey',
		'src27' => 'united arab emirates',
		'src15' => 'w. med - spain/balearics',
		'src1' => 'w. med -naples/sicily',
		'src6' => 'w. med -riviera/cors/sard.',
	);

	return $specific_locations;
}

add_action( 'admin_init', 'create_location_pages' );
function create_location_pages($array = null) {
	$locations = set_locations();

	foreach ($locations as $key => $value) {
		$post_id = str_replace('src', '', $key);

		if ( FALSE === get_post_status( $post_id ) ) {
			$args = array(
		        'post_title'     => $value,
		        'import_id'      => $post_id,
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
	        }
		} else {
		  // The post exists
		}

	}
}

function format_shitty_cya_feed_locations() {
	global $locations;

	$not_shitty_locations_array = [];

	foreach ($locations as $location) {
		$not_shitty_locations_array[$location['locationCode']] = strtolower($location['locationName']);
	}

	return $not_shitty_locations_array;
}

/*Get locations*/
function get_location_codes($summer_locations, $winter_locations) {
	global $formatted_shitty_cya_feed_locations;

	$summer_locations = apply_filters('iyc_summer_locations', explode(", ", $summer_locations));
	$winter_locations = apply_filters('iyc_winter_locations', explode(", ", $winter_locations));

	$winter_locations = [];

	$locations = apply_filters('iyc_locations', array_unique(array_merge($summer_locations, $winter_locations)));

	foreach ($locations as $location) {
		$location_codes[] = array_search($location, $formatted_shitty_cya_feed_locations);
	}

	return apply_filters('iyc_location_codes', $location_codes);
}

/*Get locations*/
function get_locations($summer_locations, $winter_locations) {
	var_dump(get_location_codes($summer_locations, $winter_locations));
	die;

	$locations_summer = strtolower((string)$summer_locations);
	$locations_winter = strtolower((string)$winter_locations);

	$locations = array_unique(array_merge(explode(", ",$locations_summer), explode(", ",$locations_winter)));

	$locations_to_add = array();

	foreach ($locations as $location) {
		$location = trim($location);

		if (array_search($location, $specific_locations, true) !== false && !in_array($location, $locations_to_add)) {
			$locations_to_add[] = $location;
		}
	}

	return $locations_added = implode(", ", $locations_to_add);
}



/*--------------------------------------------------------------
##Yacht Specifics
--------------------------------------------------------------*/
//Must use within the custom post type loop yacht_feed or where you can get the current boat ID
function create_boat_array() {
	$args = array( 'post_type' => 'yacht_feed', 'posts_per_page' => 999 );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();

		$yacht_id = get_the_ID();
		$yacht_title = get_the_title();
		$yacht_type = get_field('boat_type', $yacht_id);
		$staterooms = get_field('staterooms', $yacht_id);

		if (get_field('price_from', $yacht_id) && !get_field('price_from_iyc', $yacht_id)) {
			$yacht_pricef = (int)get_field('price_from', $yacht_id);
		} else {
			$yacht_pricef = get_field('price_from_iyc', $yacht_id);
		}

		if (get_field('price_to', $yacht_id) && !get_field('price_to_iyc', $yacht_id)) {
			$yacht_pricet = (int)get_field('price_to', $yacht_id);
		} else {
			$yacht_pricet = get_field('price_to_iyc', $yacht_id);
		}
		
		$yacht_guests = get_field('guests', $yacht_id);
		$yacht_length = (int)str_replace('ft', '', strtolower((string)get_field('length_feet', $yacht_id)));
		$yacht_locations = get_field('locations', $yacht_id);
		$yacht_url = get_the_permalink();

		if (get_the_post_thumbnail_url()) {
			$yacht_image = get_the_post_thumbnail_url();
		} else {
			$yacht_image = danzerpress_no_image();
		}

		$yacht_boat = array(
			'id' => $yacht_id,
			'yacht_image' => $yacht_image,
			'yacht_name' => strtolower($yacht_title),
			'yacht_url' => $yacht_url,
			'location' => $yacht_locations,
			'boat_type' => strtolower($yacht_type),
			'staterooms' => $staterooms,
			'price_from' => $yacht_pricef,
			'price_to' => $yacht_pricet,
			'guests' => $yacht_guests,
			'length' => $yacht_length,
			'locations' => $yacht_locations,
		);

		
		$yacht_boats[] = $yacht_boat;
		
	endwhile;

	//reset post wp post data
	wp_reset_postdata();

	return $yacht_boats;
}

function create_boat($args) {
	$defaults = [
		'yacht_url' => '',
		'yacht_name' => '',
		'price_to' => '',
		'price_from' => '',
		'guests' => '',
		'staterooms' => '',
		'boat_type' => '',
		'length' => '',
		'yacht_image' => ''
	];
	$args = wp_parse_args($args, $defaults);

	extract($args);

	echo '<div class="danzerpress-col-2 wow fadeIn danzerpress-flex-column" style="margin-bottom: 20px;">';
		echo '<div class="danzerpress-box" style="line-height: inherit; padding: 0px;">';
			echo '<div style="overflow:hidden; line-height: 0px;"><a href="' . $yacht_url . '"><img style="height: 100%;max-height: 200px;object-fit: cover;width: 100%;" src="' . $yacht_image . '"></a></div>';
			echo '<table class="yacht-specs danzerpress-box danzerpress-white danzerpress-shadow-3" style="margin:0px;">';
				echo '<tr><td style="">Name</td><td style="color:black;font-weight:700;">' . ucwords($yacht_name) . '</td></tr>';
				echo '<tr><td>Price From</td><td> $' . number_format($price_from) . ' - $' . number_format($price_to) . '</td></tr>';
				echo '<tr><td>Guests</td><td>' . $guests . '</td></tr>';
				echo '<tr><td>Staterooms</td><td>' . $staterooms . '</td></tr>';
				echo '<tr><td>Yacht Type</td><td>' . ucfirst($boat_type) . '</td></tr>';
				echo '<tr><td>Yacht length</td><td>' . $length . ' ft</td></tr>';
			echo '</table>';
			echo '<a style="display:block;border-radius:0px;" class="danzerpress-button-modern" href="' . $yacht_url . '">View Yacht</a>';
		echo '</div>';
	echo '</div>';
}

function set_pricing_options() {
	$pricing_options = array(
		'0' => 'All',
		'1' => '$9,999 and less',
		'2' => '$10,000 - $19,999',
		'3' => '$20,000 - $29,999',
		'4' => '$30,000 - $49,999',
		'5' => '$50,000 - $99,999',
		'6' => '$100,000 and above'
	);

	return $pricing_options;
}

function set_boat_types() {
	$boat_types = array(
		'all' => 'All Types',
		'power' => 'Power Yacht',
		'motor' => 'Motor Yacht',
		'sail' => 'Sailing Yacht',
		'cat' => 'Sailing Catamaran',
	);

	return $boat_types;
}

function set_boat_lengths() {
	$boat_lengths = array(
		'0' => 'All Lengths',
		'1' => 'Under 50 feet',
		'2' => '50 to 59 feet',
		'3' => '60 - 79 feet',
		'4' => '80 - 99 feet',
		'5' => '100 - 119 feet',
		'6' => '120 - 139 feet',
		'7' => '140 - 169 feet',
		'8' => '170 - 199 feet',
		'9' => '200 feet and larger',
	);

	return $boat_lengths;
}



/*--------------------------------------------------------------
##CYA Specifics
--------------------------------------------------------------*/
function cya_feed_content($yacht_id) {

	//Convert to XML Object
	$xml_ebrochure = xml_json_decode($yacht_id);

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
		'locations_added' => get_location_codes($xml_ebrochure['yachtSummerArea'], $xml_ebrochure['yachtWinterArea']),
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
function update_cya_acf_fields() {

}

/**
 * Created: 4/8/2019
 * Update ACF field or post meta
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

function update_yacht_changes() {
	$yacht_changes = array(
		'yacht_id' => '',
		'content_old' => '',
		'content_new' => '',
	);

	return $yacht_changes;
}

/*Check for similarties of new vs old content*/
function check_similarities($content_new, $content_old, $field_id = null, $post_id = null, $my_post = null) {
	$content_stripped_1 = strip_tags(trim((string)$content_new));
	$content_stripped_1 = preg_replace('/\s+/', '', $content_stripped_1);

	$content_stripped_2 = strip_tags(trim((string)$content_old));
	$content_stripped_2 = preg_replace('/\s+/', '', $content_stripped_2);

	if ($content_stripped_1 === $content_stripped_2) {
		//Do Nothing
	} else {

		if ($field_id !== FALSE) {
			
			$update_yacht = update_yacht_changes();

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
	$xml = get_xml_snapin_url();

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