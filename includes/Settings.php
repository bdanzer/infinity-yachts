<?php
namespace IYC;

class Settings
{
    public function __construct() 
    {
        add_action('admin_init', [$this, 'danzerpress_register_settings']);
        add_action('admin_notices', [$this, 'danzerpress_admin_notices']);
    }

    public static function locations_settings() 
    {
        echo 'Hi';
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
        
        echo '<p>'. esc_html__('These settings enable you to customize the CYA feed.', 'danzerpress') .'</p>'; 

        echo '<div style="background:white;padding:20px;box-shadow:1px 1px 1px rgba(0, 0, 0, 0.050980392156862744);overflow:hidden;">';

            echo '
            <label>Yacht Locations
            <select id="ylocations" name="ylocations" style="display:block;">
                <option id="0" value="0">Select Location</option>
                <option id="src13" value="src13">Alaska</option>
                <option id="src28" value="src28">Antarctica</option>
                <option id="src29" value="src29">Arctic</option>
                <option id="src21" value="src21">Australia</option>
                <option id="src5" value="src5">Bahamas</option>
                <option id="src17" value="src17">California</option>
                <option id="src34" value="src34">Canary Islands</option>
                <option id="src7" value="src7">Caribbean Leewards</option>
                <option id="src3" value="src3">Caribbean Virgin Islands</option>
                <option id="src8" value="src8">Caribbean Windwards</option>
                <option id="src20" value="src20">Central America</option>
                <option id="src16" value="src16">Croatia</option>
                <option id="src32" value="src32">Cuba</option>
                <option id="src26" value="src26">Dubai</option>
                <option id="src10" value="src10">Florida</option>
                <option id="src30" value="src30">French Polynesia</option>
                <option id="src31" value="src31">Galapagos</option>
                <option id="src18" value="src18">Great Lakes</option>
                <option id="src4" value="src4">Greece</option>
                <option id="src12" value="src12">Indian Ocean and SE Asia</option>
                <option id="src19" value="src19">Mexico</option>
                <option id="src9" value="src9">New England</option>
                <option id="src22" value="src22">New Zealand</option>
                <option id="src24" value="src24">Northern Europe</option>
                <option id="src14" value="src14">Pacific NW</option>
                <option id="src25" value="src25">Red Sea</option>
                <option id="src2" value="src2">South America</option>
                <option id="src33" value="src33">South China Sea</option>
                <option id="src23" value="src23">South Pacific</option>
                <option id="src11" value="src11">Turkey</option>
                <option id="src27" value="src27">United Arab Emirates</option>
                <option id="src15" value="src15">W. Med - Spain/Balearics</option>
                <option id="src1" value="src1">W. Med -Naples/Sicily</option>
                <option id="src6" value="src6">W. Med -Riviera/Cors/Sard.</option>
            </select>
            </label>
            ';


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