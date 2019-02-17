<?php // Danzerpress CYA Feeds - Validate Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}



// callback: validate options
function danzerpress_callback_validate_options( $input ) {

	$old_data = get_option('danzerpress_options');
	if (empty($old_data)) {
		$old_data = array();
	}

	if (empty($input)) {
		$input = array();
	}

	$input = array_merge($old_data, $input);
	//var_dump($input);

	return $input;

}


