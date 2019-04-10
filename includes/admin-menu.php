<?php // Danzerpress CYA Feeds - Admin Menu



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}

/* 
	
	add_menu_page(
		string   $page_title, 
		string   $menu_title, 
		string   $capability, 
		string   $menu_slug, 
		callable $function = '', 
		string   $icon_url = '', 
		int      $position = null 
	)
	
	*/

// add top-level administrative menu
function http_get_add_toplevel_menu() {

	if (current_user_can( 'moderate_comments' )) { 
		add_menu_page(
			'DanzerPress CYA Yachts',
			'CYA Yachts',
			'moderate_comments',
			'danzerpress',
			[IYC\Settings::class, 'danzerpress_display_settings_page'],
			get_home_url() . '/wp-content/uploads/2018/01/danzerpressofficial-e1519213344817.png',
			null
		);

		/**
		 * Slug
		 * page title
		 * menu title
		 * capability
		 * menu slug
		 * function
		 */
		add_submenu_page(
			'danzerpress', 
			'Location Manager', 
			'Locations', 
			'moderate_comments', 
			'iyc_locations', 
			[IYC\Settings::class, 'locations_settings']
		);
	}

}

if (is_admin()) {
	add_action( 'admin_menu', 'http_get_add_toplevel_menu' );
}
