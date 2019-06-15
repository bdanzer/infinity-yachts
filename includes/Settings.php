<?php
namespace IYC;

use IYC\helpers\YachtHelper;
use Timber;

class Settings
{
    public function __construct() 
    {
        add_action('admin_init', [$this, 'danzerpress_register_settings']);
        add_action('admin_notices', [$this, 'danzerpress_admin_notices']);
        add_action('admin_post_locations_to_add', [$this, 'validate_locations_to_add']);
        add_action('admin_post_locations_to_add_manually', [$this, 'validate_locations_to_add_manually']);
    }

    public function validate_locations_to_add_manually() 
    {
        if ( ! isset( $_POST['locations_to_add_manually_nonce'] ) 
        || ! wp_verify_nonce( $_POST['locations_to_add_manually_nonce'], 'iyc_adding_locations_manually' ) 
        ) {
            wp_die('Sorry, your nonce did not verify.');
            exit;
        }
        
        $location_to_add_manually = sanitize_text_field($_POST['manual_location']['location_to_add_manually']);

        $current_locations = get_option('IYC_cya_locations');

        $number = count($current_locations) + 1;
        $current_locations["iyc{$number}"] = $location_to_add_manually;

        update_option('IYC_cya_locations', $current_locations);
        create_location_page("iyc{$number}", $location_to_add_manually);

        wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
        exit;
    }

    public function validate_locations_to_add() 
    {
        if ( ! isset( $_POST['locations_to_add_nonce'] ) 
        || ! wp_verify_nonce( $_POST['locations_to_add_nonce'], 'iyc_adding_locations' ) 
        ) {
            wp_die('Sorry, your nonce did not verify.');
            exit;
        }

        $locations_to_add = [];

        foreach ($_POST['danzerpress_options'] as $location_key => $location) {
            $locations_to_add[ sanitize_text_field($location_key) ] = sanitize_text_field($location);
        }

        $current_locations = get_option('IYC_cya_locations');
        
        $locations_removed = array_diff($current_locations, $locations_to_add);
        $locations_added = array_diff($locations_to_add, $current_locations);

        /**
         * Remove location pages
         */
        if (!empty($locations_removed) && is_array($locations_removed)) {
            foreach ($locations_removed as $location_key => $location) {
                $post_id = get_post_id_from_dest_key($location_key);
                
                if (!empty($post_id)) {
                    wp_delete_post($post_id, true);
                }
            }
        }
        
        if (!empty($locations_added) && is_array($locations_added)) {
            foreach ($locations_added as $location_key => $location) {
                create_location_page($location_key, $location);
            }
        }

        update_option('IYC_cya_locations', $locations_to_add);

        wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
        exit;
    }

    /**
     * TODO: Should probably store in an option and set up a cron to check for updates to the destinaions 
     * instead of the merging
     */
    public static function locations_settings() 
    {
        $current_locations = YachtHelper::get_locations();
        $context = [
            'locations' => YachtHelper::format_shitty_cya_feed_locations() + $current_locations,
            'current_locations' => $current_locations
        ];

        $checkboxes = Timber::compile('parts/check-boxes.twig', $context);
        
        echo '<h2>Locations to add from feed</h2>';
        echo '<p>Locations selected here are what will be added to the site from cya feed</p>';
        echo '<form method="POST" action="' . esc_url( admin_url('admin-post.php') ) . '">';
        echo $checkboxes;
        echo '<input type="hidden" name="action" value="locations_to_add">';
        wp_nonce_field('iyc_adding_locations', 'locations_to_add_nonce');
        echo '<div style="clear: both;">';
        submit_button();
        echo '</div>';
        echo '</form>';

        echo '<h2>Manually added locations</h2>';
        echo '<form method="POST" action="' . esc_url( admin_url('admin-post.php') ) . '">';
        echo '<input type="text" name="manual_location[location_to_add_manually]" placeholder="enter locations here">';
        echo '<input type="hidden" name="action" value="locations_to_add_manually">';
        wp_nonce_field('iyc_adding_locations_manually', 'locations_to_add_manually_nonce');
        submit_button();
        echo '</form>';
    }

    // display the plugin settings page
    public static function danzerpress_display_settings_page() {

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

    // register plugin settings
    public function danzerpress_register_settings() {
        
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
            [$this, 'danzerpress_callback_validate_options']
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
            [$this, 'danzerpress_callback_section_feed'],
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

    // callback: feed section
    public function danzerpress_callback_section_feed() {
        $context = [
            'locations' => YachtHelper::get_locations()
        ];

        echo '<p>'. esc_html__('These settings enable you to customize the CYA feed.', 'danzerpress') .'</p>'; 

        echo '<div style="background:white;padding:20px;box-shadow:1px 1px 1px rgba(0, 0, 0, 0.050980392156862744);overflow:hidden;">';
            Timber::render('parts/ylocations.twig', $context);
            echo '<div class="ajax-response"></div>';
        
        echo '</div>';
        
    }

    // callback: validate options
    public function danzerpress_callback_validate_options( $input ) {

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

}