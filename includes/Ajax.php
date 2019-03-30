<?php
namespace IYC;

class Ajax
{
    public function __construct()
    {
        // ajax hook for logged-in users: wp_ajax_{action}
        add_action('wp_ajax_admin_hook', [$this, 'ajax_admin_handler']);
        add_action('admin_enqueue_scripts', [$this, 'ajax_admin_enqueue_scripts']);
        add_action('wp_ajax_test_handler', [$this, 'test_handler']);
    }

    // enqueue scripts
    public function ajax_admin_enqueue_scripts( $hook ) {

        // check if our page
        if ( 'toplevel_page_danzerpress' !== $hook ) return;

        // define script url
        $script_url = get_iyc_url() . '/admin/js/ajax-admin.js';

        // enqueue script
        wp_enqueue_script( 'ajax-admin', $script_url, array( 'jquery' ) );

        // create nonce
        $nonce = wp_create_nonce( 'ajax_admin' );

        // define script
        $script = array( 'nonce' => $nonce );

        // localize script
        wp_localize_script( 'ajax-admin', 'ajax_admin', $script );

    }

    public function test_handler() {

        // check nonce
        check_ajax_referer( 'ajax_admin', 'nonce' );

        // check user
        if ( ! current_user_can( 'moderate_comments' ) ) return;

        //recieving jquery array
        $jqueryArray = $_POST['array'];

        //getting the current database values for yachts
        $option_value = get_option('danzerpress_options');

        foreach ($jqueryArray as $value) {
            //converting value into string
            $strvalue = (string)$value;

            if (strpos($strvalue, 'unchecked') === false) {
                //if is not checked store yacht id
                $newOrder['yachtid_' . $value] = $value;
            } else {
                //if is checked store yacht id to 0
                $value = str_replace('unchecked', '', $strvalue);
                $newOrder['yachtid_' . $value] = 0;
            }
        }

        if (empty($option_value)) {
            //if empty or first item create a new array for merge
            $option_value = array();
        }

        //merging items so no overrites occur
        $merge = array_merge($option_value, $newOrder);

        //updating database manually for the form with correct values
        update_option( 'danzerpress_options', $merge);

        foreach ($jqueryArray as $value) {

            //converting the value to be a string
            $strvalue = (string)$value;

            //checking if the string 'unchecked is attached';
            $is_checked = strpos($strvalue, 'unchecked');

            //removing the unchecked identifer to single out number
            $post_id = str_replace('unchecked', '', $is_checked);

            //converting string into number to avoid potential errors
            $post_id = (int)$value;

            if ( $is_checked !== false) {
                
                //find image to delete
                $image_id = get_post_thumbnail_id( $post_id );

                //delete media associated with post
                wp_delete_attachment($image_id, true);

                //delete post if is unchecked
                wp_delete_post( $post_id );
            }
            
            if ( get_post_status ( $value ) || $is_checked !== false ) {
                //Do nothing if it's a post already
            } elseif ( $is_checked === false ) {
                //Create post if it does not exist


                //Get boat content array
                $cya_feed_content = cya_feed_content($post_id);

                //Set post settings for creation
                $post_arr = array(
                    'import_id' => $post_id,
                    'post_type' => 'yacht_feed',
                    'post_title'   => (string)ucfirst(strtolower($cya_feed_content['name'])),
                    'post_content' => $post_id,
                    'post_status'  => 'publish',
                    'post_author'  => get_current_user_id(),
                );

                //Create post
                wp_insert_post($post_arr);

                //Upload media url to wordpress
                $return = media_sideload_image($cya_feed_content['image'], $post_id, $cya_feed_content['desc'], 'id');

                //Set the media url to the post
                set_post_thumbnail($post_id, $return);

                //get fields
                $acf_fields = cya_acf_fields($cya_feed_content);

                //Update acf fields
                foreach ($acf_fields as $acf_array) {
                    $content = $acf_array[0];
                    $field_key = $acf_array[1];

                    //update_field($selector, $value, $post_id);
                    update_field( $field_key, $content, $post_id );
                }
            } 	
        }
        wp_die();
    }

    // process ajax request
    public function ajax_admin_handler() {

        // check nonce
        check_ajax_referer( 'ajax_admin', 'nonce' );

        // check user
        if ( ! current_user_can( 'moderate_comments' ) ) return;

        // define the url
        $result = isset( $_POST['ylocations'] ) ? $_POST['ylocations'] : false;

        // get url response
        $response = get_xml_snapin_url() .'&ylocations=' . $result;


        if ( ! empty( $result ) ) {

            $xml_snapins = simplexml_load_file($response);
            $xml_object = $xml_snapins->yacht;

            foreach ($xml_object as $value) {
                $option_value = get_option('danzerpress_options');

                if (isset($option_value['yachtid_' . $value->yachtId])) {
                    $checked = checked($option_value['yachtid_' . $value->yachtId],$value->yachtId, false);
                } else {
                    $checked = '';
                }

                echo '
                <div class="yacht-wrap" style="width:25%; float:left;"> 
                    <input type="hidden" value="0" name="danzerpress_options[yachtid_' . $value->yachtId . ']">
                    <input id="' . $value->yachtId . '" name="danzerpress_options[yachtid_' . $value->yachtId . ']" type="checkbox" ' . $checked . ' value="' . $value->yachtId . '">
                    <div class="yacht-content">
                        <h4 style="margin-bottom:0px;">' . $value->yachtName . '</h4>
                        <p style="margin-top:0px;">Size: ' . $value->size . '</p>
                    </div>
                </div>
                ';
            }

        } else {

            echo 'No results. Please check the URL and try again.';

        }

        // end processing
        wp_die();
    }
}