<?php 
namespace IYC\assets;

class Assets 
{
    public function __construct() 
    {
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    public function admin_scripts() 
    {
        wp_enqueue_style('admin_css', get_iyc_url() . '/resources/css/admin/admin.css');
    }
}