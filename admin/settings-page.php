<?php // DanzerPress CYA Feeds - Settings Page



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}


// display the plugin settings page
function danzerpress_display_settings_page() {

	// check if user is allowed access
	if ( ! current_user_can( 'moderate_comments' )) return;
	?>

	<div class="wrap">
		<form id="yacht-form" action="options.php" method="post">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<?php 

		// output security fields
		settings_fields( 'danzerpress_options' );
			
		// output setting sections
		do_settings_sections( 'danzerpress' );

		// submit button
        submit_button();

		echo '<div class="danzerpress-save"></div>';

		?>

		</form>

	</div>

<?php

}


// display admin notices
function danzerpress_admin_notices() {
	
	// get the current screen
	$screen = get_current_screen();
	
	// return if not danzerpress settings page
	if ( $screen->id !== 'toplevel_page_danzerpress' ) return;
	
	// check if settings updated
	if ( isset( $_GET[ 'settings-updated' ] ) ) {
		
		// if settings updated successfully
		if ( 'true' === $_GET[ 'settings-updated' ] ) : 
		
		?>
			
			<div class="notice notice-success is-dismissible">
				<p><strong><?php _e( 'Congratulations, you are awesome!', 'danzerpress' ); ?></strong></p>
			</div>
			
		<?php 
		
		// if there is an error
		else : 
		
		?>
			
			<div class="notice notice-error is-dismissible">
				<p><strong><?php _e( 'Houston, we have a problem.', 'danzerpress' ); ?></strong></p>
			</div>
			
		<?php 
		
		endif;
		
	}
	
}
add_action( 'admin_notices', 'danzerpress_admin_notices' );
