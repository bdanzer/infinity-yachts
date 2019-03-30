<?php 
function public_ajax() {
    wp_enqueue_script( 'ajax-public-script', plugins_url('/js/public.js', __FILE__) );
    if (is_page('search')) {
        wp_enqueue_script( 'ajax-script', plugins_url('/js/load-boats.js', __FILE__) );
        wp_localize_script( 'ajax-script', 'ajax_url', array( 
            'ajaxurl' => admin_url( 'admin-ajax.php'),
            'security' => wp_create_nonce( 'my-special-string' )
        ));
    }
}
add_action('wp_enqueue_scripts', 'public_ajax');



if (is_admin()) {
    add_action( 'wp_ajax_form_start', 'form_start' );
    add_action( 'wp_ajax_nopriv_form_start', 'form_start' );
}
function form_start() {

	check_ajax_referer( 'my-special-string', 'security' );

	$locations = set_locations();

	$yacht_locations_post = $_POST['ylocations'];

	$yachts_removed = array();
	$remove_yacht = array(
		'yacht_id' => '',
	);

	$yacht_name_post = strtolower((string)$_POST['yachtName']);

	if ($yacht_name_post == '') {
		$yacht_name_post = 'all';
	}

	$type = $_POST['boatType'];

	$staterooms = (int)$_POST['staterooms'];

	$guests = (int)$_POST['guests'];

	$price = $_POST['price'];
	$price = str_replace("-"," ", $price);

	switch ($price) {
		case 0:
	    	$price_from = 1;
	    	$price_to = 50000;
	    	break;
	    case 1:
	        $price_from = 1;
	        $price_to = 9999;
	        break;
	    case 2:
	        $price_from = 10000;
	        $price_to = 19999;
	        break;
	    case 3:
	        $price_from = 20000;
	        $price_to = 29999;
	        break;
	    case 4:
	        $price_from = 30000;
	        $price_to = 49999;
	        break;
	    case 5:
	        $price_from = 50000;
	        $price_to = 100000;
	        break;
	    case 6:
	        $price_from = 100001;
	        $price_to = 999999;
	        break;
	}

	$length = $_POST['yachtLen'];

	switch ($length) {
		case 0:
			$length_from = 'all';
			$length_to = 'all';
			break;
		case 1:
	    	$length_from = 1;
	    	$length_to = 49;
	    	break;
	    case 2:
	        $length_from = 50;
	        $length_to = 59;
	        break;
	    case 3:
	        $length_from = 60;
	        $length_to = 79;
	        break;
	    case 4:
	        $length_from = 80;
	        $length_to = 99;
	        break;
	    case 5:
	        $length_from = 100;
	        $length_to = 119;
	        break;  
	    case 6:
	        $length_from = 120;
	        $length_to = 139;
	        break;
	    case 7:
	        $length_from = 140;
	        $length_to = 169;
	        break;
	    case 8:
	        $length_from = 170;
	        $length_to = 199;
	        break;
	    case 9:
	        $length_from = 200;
	        $length_to = 50000;
	        break;    
	}

	//Creating a results array
	$results_array = array(
		'yacht_name' => $yacht_name_post,
		'location' => $yacht_locations_post,
		'boat_type' => $type, 
		'staterooms' => $staterooms, 
		'guests' => $guests,
		'price_from' => $price_from,
		'price_to' => $price_to,
		'length_from' => $length_from,
		'length_to' => $length_to,
	);

	//Looping through database of yachts and returning all boats
	$yacht_boats = create_boat_array();

	//Can add broader filters here like filter by price
	//usort($yacht_boats, build_sorter('price_from'));

	//Check for filters
	foreach ($yacht_boats as $yacht_boat) {
		//Check Locations
	    $yacht_locations = explode(', ', $yacht_boat['location']);
	    $location_codes = array();

	    foreach ($yacht_locations as $yacht_location) {
	    	$key = array_search($yacht_location, $locations);
	    	$location_codes[] = trim($key);
	    }

	    if ($yacht_locations_post && in_array($yacht_locations_post, $location_codes) === false) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
	    }

	    //Check for if price is enabled
	    if ($price != 0 && in_array($yacht_boat['price_from'], range($price_from, $price_to)) === false) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
		}

	    //Check for if boat type is enabled
	    if ($type != 'all' && $results_array['boat_type'] != $yacht_boat['boat_type']) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
	    }

	    //Check for if guests is enabled
	    if ($results_array['guests'] != 0 && $results_array['guests'] != $yacht_boat['guests']) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
	    } 

	    //Check for if staterooms is enabled
	    if ($results_array['staterooms'] != 0 && $results_array['staterooms'] != $yacht_boat['staterooms']) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
	    }

	    //Check for if Boat Name is enabled
	    if(strpos($yacht_boat['yacht_name'], $yacht_name_post) === FALSE && $yacht_name_post != 'all'){
			$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
		}

		//Check for if length is enabled
	    if ($length != 0 && in_array($yacht_boat['length'], range($results_array['length_from'], $results_array['length_to'])) === false) {
	    	$remove_yacht = array(
	    		'yacht_id' => $yacht_boat['id'], 
	    	);
		}

		//Remove posts that don't meat criteria
		if ($remove_yacht['yacht_id'] != $yacht_boat['id']){
			create_boat($yacht_boat);

			$yachts_removed[] = $yacht_boat['id'];
		}

	}

	//If no yachts removed then no results
	if (empty($yachts_removed)) {
		echo '<div class="danzerpress-box danzerpress-white" style="border-left: 2px solid red;">';
			echo '<h4>No Results Found...</h4>';
		echo '</div>';
	}
	

	wp_die();
}