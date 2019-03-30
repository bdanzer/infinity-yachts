<?php
namespace IYC;

class IYC 
{
    public function __construct()
    {
        add_action('init', [$this, 'yacht_setup_post_type']);
        add_action('init', [$this, 'review_setup_post_type']);
        add_action('admin_enqueue_scripts', [$this, 'load_custom_wp_admin_style']);
    }

    public function yacht_setup_post_type() {
        $args = array(
            'public'    => true,
            'label'     => __( 'Yacht Feed', 'textdomain' ),
            'menu_icon' => 'dashicons-book',
            'has_archive' => false,
            'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
            'rewrite' => array(
                'slug' => 'yacht',
            ),
        );
        register_post_type( 'yacht_feed', $args );
    }
    
    public function review_setup_post_type() {
        $args = array(
            'public'    => true,
            'label'     => __( 'Reviews', 'textdomain' ),
            'menu_icon' => 'dashicons-book',
            'has_archive' => 'reviews',
            'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
            'rewrite' => array(
                'slug' => 'review',
            ),
        );
        register_post_type( 'review_feed', $args );
    }

    public function load_custom_wp_admin_style($hook) {
        // Load only on ?page=mypluginname
        if($hook != 'toplevel_page_danzerpress') {
                return;
        }
        wp_enqueue_style( 'custom_wp_admin_css', get_iyc_url() . '/admin/css/danzerpress-admin.css');
    }

    public static function get_url() 
    {
        return IYC_PLUGIN_URL;
    }

    public static function get_dir() 
    {
        return IYC_PLUGIN_DIR;
    }
}