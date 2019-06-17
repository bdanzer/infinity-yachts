<?php
namespace IYC;

use Timber\Timber;

class Ajax
{
    public function __construct()
    {
        if (is_admin()) {
            $this->admin();
        }

        $this->public();
    }

    public function admin() 
    {
        add_action('admin_enqueue_scripts', [$this, 'ajax_admin_enqueue_scripts']);

        add_action('wp_ajax_form_start', [$this, 'search_results']);
        add_action('wp_ajax_nopriv_form_start', [$this, 'search_results']);

        add_action('wp_ajax_load_more', [$this, 'load_more_boats']);
        add_action('wp_ajax_nopriv_load_more', [$this, 'load_more_boats']);

        add_action('wp_ajax_admin_hook', [$this, 'get_checked_boats']);

        add_action('wp_ajax_test_handler', [$this, 'insert_checked_boat']);
    }

    public function load_more_boats() 
    {
        check_ajax_referer('IYC_YACHTS_FETCH', 'security');

        $args = array(
            'post_type'  => 'yacht_feed',
            'meta_query' => yacht_meta_query($_POST),
            'meta_key' => 'price_from',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'paged' => intval($_POST['paged']),
            'posts_per_page' => 10
        );

        // var_dump($args);
        // die;
    
        $posts = Timber::get_posts($args);
    
        $template = (empty($posts)) ? 'parts/no-yachts.twig' : 'parts/boat-collection.twig';
    
        $context = get_context() + [
            'posts' => $posts
        ];
    
        Timber::render($template, $context);
        
        wp_die();
    }

    public function public() 
    {
        add_action('wp_enqueue_scripts', [$this, 'public_ajax_scripts']);
    }

    public function public_ajax_scripts() 
    {
        wp_enqueue_script('ajax-public-script', get_iyc_url() . '/resources/js/public/public.js');
        wp_localize_script('ajax-public-script', 'ajax_url', array( 
            'ajaxurl' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('IYC_YACHTS_FETCH')
        ));
    }

    // enqueue scripts
    public function ajax_admin_enqueue_scripts( $hook ) 
    {

        // check if our page
        if ( 'toplevel_page_danzerpress' !== $hook ) return;

        // define script url
        $script_url = get_iyc_url() . '/resources/js/admin/ajax-admin.js';

        // enqueue script
        wp_enqueue_script( 'ajax-admin', $script_url, array( 'jquery' ) );

        // create nonce
        $nonce = wp_create_nonce( 'ajax_admin' );

        // define script
        $script = array( 'nonce' => $nonce );

        // localize script
        wp_localize_script( 'ajax-admin', 'ajax_admin', $script );

    }

    public function insert_checked_boat() 
    {
        // check nonce
        check_ajax_referer('ajax_admin', 'nonce');

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
        update_option('danzerpress_options', $merge);

        foreach ($jqueryArray as $value) {

            //converting the value to be a string
            $strvalue = (string)$value;

            //checking if the string 'unchecked is attached';
            $is_checked = strpos($strvalue, 'unchecked');

            //removing the unchecked identifer to single out number
            $yacht_id = intval((int)str_replace('unchecked', '', $strvalue));

            //we need to find the id if is_checked not false
            $post_id = get_post_id_from_yacht_id($yacht_id);

            if ($is_checked !== false) {

                if (!$post_id)
                    continue;
                
                //find image to delete
                $image_id = get_post_thumbnail_id($post_id);

                //delete media associated with post
                wp_delete_attachment($image_id, true);

                //delete post if is unchecked
                wp_delete_post($post_id);
            }
            
            if ( get_post_status ( $post_id ) || $is_checked !== false ) {
                //Do nothing if it's a post already
            } elseif ( $is_checked === false ) {
                //Create post if it does not exist

                //Get boat content array
                $cya_feed_content = cya_feed_content($yacht_id);

                //Set post settings for creation
                $post_arr = array(
                    'post_type' => 'yacht_feed',
                    'post_title'   => (string)ucwords(strtolower($cya_feed_content['name'])),
                    'post_content' => $yacht_id,
                    'post_status'  => 'publish',
                    'post_author'  => get_current_user_id(),
                );

                //Create post
                $post_id = wp_insert_post($post_arr);

                if (!$post_id) {
                    continue;
                }

                update_post_meta($post_id, 'yacht_id', $yacht_id);

                if (empty($cya_feed_content['desc'])) {
                    $cya_feed_content['desc'] = $cya_feed_content['name'];
                }

                //Upload media url to wordpress
                $image_id = media_sideload_image($cya_feed_content['image'], $post_id, $cya_feed_content['desc'], 'id');
                
                //Set the media url to the post
                set_post_thumbnail($post_id, $image_id);

                //get fields
                $acf_fields = cya_acf_fields($cya_feed_content);

                iyc_update_meta($acf_fields, $post_id);
            } 	
        }
        wp_die();
    }

    // process ajax request
    /**
     * TODO: could use timber render and clean up the response array for snyachts
     */
    public function get_checked_boats() 
    {
        // check nonce
        check_ajax_referer( 'ajax_admin', 'nonce' );

        // check user
        if ( ! current_user_can( 'moderate_comments' ) ) return;

        // define the url
        $result = isset( $_POST['ylocations'] ) ? $_POST['ylocations'] : false;

        // get url response
        $response = API::get_xml_snyachts(['ylocations' => $result]);

        if ( ! empty( $result ) ) {

            if (!isset($response['yacht']['yachtId'])) {
                $response = $response['yacht'];
            }

            foreach ($response as $value) {
                if (!is_array($value)) {
                    $value = $response['yacht'];
                }
                $option_value = get_option('danzerpress_options');

                if (isset($option_value['yachtid_' . $value['yachtId']])) {
                    $checked = checked($option_value['yachtid_' . $value['yachtId']],$value['yachtId'], false);
                } else {
                    $checked = '';
                }

                echo '
                <div class="yacht-wrap" style="width:25%; float:left;"> 
                    <input type="hidden" value="0" name="danzerpress_options[yachtid_' . $value['yachtId'] . ']">
                    <input id="' . $value['yachtId'] . '" name="danzerpress_options[yachtid_' . $value['yachtId'] . ']" type="checkbox" ' . $checked . ' value="' . $value['yachtId'] . '">
                    <div class="yacht-content">
                        <h4 style="margin-bottom:0px;">' . $value['yachtName'] . '</h4>
                        <p style="margin-top:0px;">Size: ' . $value['size'] . '</p>
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

    public function search_results() 
    {
        check_ajax_referer('IYC_YACHTS_FETCH', 'security');
    
        /**
         * Let's filter for name if we get it
         */
        if (isset($_POST['yachtName']) && !empty($_POST['yachtName'])) {
            add_filter('posts_where_request', 'yacht_feed_search', 10, 2);
        }

        $args = array(
            'post_type'  => 'yacht_feed',
            'meta_query' => yacht_meta_query($_POST),
            'meta_key' => 'price_from',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'posts_per_page' => 10,
            'paged' => 1
        );
    
        $posts = Timber::get_posts($args);
    
        /**
         * lets remove after we call the query 
         * so we don't cause any other filters to run
         */
        remove_filter('posts_where_request', 'yacht_feed_search');

        $template = (empty($posts)) ? 'parts/no-yachts.twig' : 'parts/boat-collection.twig';
    
        $context = get_context() + [
            'posts' => $posts,
            'loadmore' => true
        ];
    
        Timber::render($template, $context);
        
        wp_die();
    }
}