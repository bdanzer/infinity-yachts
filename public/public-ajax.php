<?php 
function public_ajax() {
    wp_enqueue_script( 'ajax-public-script', plugins_url('/js/public.js', __FILE__) );
    if (is_page('search')) {
        wp_enqueue_script( 'ajax-script', plugins_url('/js/load-boats.js', __FILE__) );
        wp_localize_script( 'ajax-script', 'ajax_url', array( 
            'ajaxurl' => admin_url( 'admin-ajax.php'),
            'security' => wp_create_nonce( 'IYC_YACHTS_FETCH' )
        ));
    }
}
add_action('wp_enqueue_scripts', 'public_ajax');

if (is_admin()) {
    add_action( 'wp_ajax_form_start', 'form_start' );
    add_action( 'wp_ajax_nopriv_form_start', 'form_start' );
}

function form_start() {
	check_ajax_referer('IYC_YACHTS_FETCH', 'security');

	$price_arr = get_yacht_price($_POST['price']);
	$length_arr = get_yacht_length($_POST['yachtLen']);

	/**
	 * Let's filter for name if we get it
	 */
	if (isset($_POST['yachtName']) && !empty($_POST['yachtName'])) {
		add_filter('posts_where_request', 'yacht_feed_search', 10, 2);
	}

	$meta_query = [];
	$locations = set_locations();

	$boat_meta = [
		'locations' => $locations[$_POST['ylocations']],
		'boat_type' => $_POST['boatType'], 
		'staterooms' => $_POST['staterooms'], 
		'guests' => $_POST['guests'],
		'price_from' => [
			'from' => $price_arr['price_from'],
			'to' => $price_arr['price_to']
		],
		'length_feet' => [
			'from' => $length_arr['length_from'],
			'to' => $length_arr['length_to']
		]
	];

	/**
	 * Setting up meta_query
	 * Not the cleanest but seems most DRY
	 */
	foreach ($boat_meta as $variable => $value) {
		if ($value['to'] === 'all' || $value === 'all')
			continue;
		
		if (empty($value))
			continue;

		/**
		 * For now handles the price_from/length_from logic
		 */
		if (is_array($value)) {
			$meta_query[] = [
				'key'     => $variable,
				'value'   => [$value['from'], $value['to']],
				'compare' => 'BETWEEN',
				'type'    => 'numeric',
			];
		} else {
			$meta_query[] = [
				'key'     => $variable,
				'value'   => $value,
			];
		}
	}

	$args = array(
        'post_type'  => 'yacht_feed',
        'meta_query' => $meta_query
	);

	$posts = Timber::get_posts($args);

	/**
	 * lets remove after we call the query 
	 * so we don't cause any other filters to run
	 */
	remove_filter('posts_where_request', 'yacht_feed_search');

	if (empty($posts)) {
		$template = 'parts/no-yachts.twig';
	} else {
		$template = 'parts/boat-collection.twig';
	}

	$context = get_context() + [
		'posts' => $posts
	];

	Timber::render($template, $context);
	
	wp_die();
}