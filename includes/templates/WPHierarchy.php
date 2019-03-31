<?php
namespace IYC\templates;

use IYC\controllers\Controller;
use IYC\IYC;

class WPHierarchy 
{
    protected $types = [
        'index',
        '404', 
        'archive', 
        'author', 
        'category', 
        'tag', 
        'taxonomy', 
        'date', 
        'embed', 
        'home', 
        'frontpage', 
        'page', 
        'paged', 
        'search', 
        'single', 
        'singular',
        'attachment'
    ];

    public function __construct() 
    {
        /**
         * This will run before template_include to set our WP Hierarchy
         */
        $this->add_filters();

        /**
         * If we get the template in the plugin 
         * then load it over anything else
         */
        add_filter('template_include', [$this, 'set_controller_wp_template'], 11);
    }

    public function add_filters() 
    {
        foreach ($this->types as $type) {
            add_filter("{$type}_template_hierarchy", [Controller::class, 'set_wp_hierarchy_stack']);
        }
    }

    /**
     * Help find and render the correct wp template for twig
     */
    public function set_controller_wp_template($t)
    {
        $controller = new Controller();
        $controller::set_wp_template($t);
        $wp_heirarchy_stack = $controller::get_wp_heirarchy_stack();

        /**
         * If we find file in plugin 
         * then break and load that file
         */
        foreach ($wp_heirarchy_stack as $template) {
            if (is_file(IYC::get_dir() . 'templates/wp-templates/' . $template)) {
                $t = IYC::get_dir() . 'templates/wp-templates/' . $template;
                break;
            }
        }
        
        $controller->render();
        return $t;
    }
}