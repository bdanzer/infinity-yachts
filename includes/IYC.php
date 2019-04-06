<?php
namespace IYC;

use Timber\Timber;

class IYC 
{
    public function __construct()
    {
        $timber = new Timber();
        $timber::$dirname = 'resources/templates';
        $timber::$locations = [
            get_iyc_dir() . 'resources/templates'
        ];

        add_action('init', [$this, 'yacht_setup_post_type']);
        add_action('init', [$this, 'review_setup_post_type']);

        new Ajax;

        // include plugin dependencies: admin only
        if ( is_admin() ) {
            $this->admin();
            add_action('admin_enqueue_scripts', [$this, 'load_custom_wp_admin_style']);
        }

        $this->public();

        add_action('plugins_loaded', [templates\PageTemplater::class, 'get_instance']);
    }

    public function admin() 
    {
        require_once get_iyc_dir() . 'includes/admin-menu.php';
        
        new Settings;
        //new IYC\WPCron;
    }

    public function public() 
    {
        new templates\WPHierarchy;
        new Twig;
        
        require_once get_iyc_dir() . 'includes/filters.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
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
        if($hook != 'toplevel_page_danzerpress')
            return;

        wp_enqueue_style('custom_wp_admin_css', get_iyc_url() . '/resources/css/admin/danzerpress-admin.css');
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