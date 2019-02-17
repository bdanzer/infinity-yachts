<?php // Danzerpress CYA Feeds - Register Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}



// register plugin settings
function danzerpress_register_settings() {
	
	/*
	
	register_setting( 
		string   $option_group, 
		string   $option_name, 
		callable $sanitize_callback = ''
	);
	
	*/
	
	register_setting( 
		'danzerpress_options', 
		'danzerpress_options', 
		'danzerpress_callback_validate_options' 
	); 
	
	/*
	
	add_settings_section( 
		string   $id, 
		string   $title, 
		callable $callback, 
		string   $page
	);
	
	*/
	
	add_settings_section( 
		'danzerpress_section_feed', 
		esc_html__('Customize feed Page', 'danzerpress'), 
		'danzerpress_callback_section_feed', 
		'danzerpress'
	);
	

	/*
	
	add_settings_field(
    	string   $id, 
		string   $title, 
		callable $callback, 
		string   $page, 
		string   $section = 'default', 
		array    $args = []
	);
	
	*/

	/*
	
	add_settings_field(
		'custom_url',
		esc_html__('Custom URL', 'danzerpress'),
		'danzerpress_callback_feed',
		'danzerpress', 
		'danzerpress_section_feed', 
		[ 'id' => 'custom_url', 'label' => esc_html__('Custom URL for the feed logo link', 'danzerpress') ]
	);

	*/
    
} 
add_action( 'admin_init', 'danzerpress_register_settings' );


