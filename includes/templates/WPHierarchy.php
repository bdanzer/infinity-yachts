<?php
namespace IYC\templates;

use IYC\controllers\Controller;
use IYC\IYC;
use Timber\Timber;

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
    protected $wp_template;
    protected $wp_template_hierarchy = [];
    protected $twig_wp_hierarchy_stack = [];
    protected $extension = '.twig';

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
            add_filter("{$type}_template_hierarchy", [$this, 'set_wp_hierarchy_stack']);
        }
    }

    public function convert_wp_hiearchy_stack_to_twig() 
    {
        /**
         * Change the php extensions to .twig
         */
        $this->twig_wp_hierarchy_stack = array_map(function($template) {
            if (false === strpos($template, $this->extension)) {
                return basename($template, '.php') . $this->extension;
            }
        }, $this->wp_template_hierarchy);
    }

    /**
     * Sets WP Template Hiearchy stack
     */
    public function set_wp_hierarchy_stack($templates) 
    {
        $this->wp_template_hierarchy = array_unique(array_merge($this->wp_template_hierarchy, $templates));
        $this->convert_wp_hiearchy_stack_to_twig();
        return $templates;
    }

    /**
     * Help find and render the correct wp template for twig
     */
    public function set_controller_wp_template($wp_template)
    {
        $this->wp_template = $wp_template;

        $controller = new Controller($this->twig_wp_hierarchy_stack);

        /**
         * If we find file in plugin 
         * then break and load that file
         */
        foreach ($this->wp_template_hierarchy as $template) {
            if (is_file(IYC::get_dir() . '/' . Timber::$dirname . '/wp-templates/' . $template)) {
                $this->wp_template = $template;
                $wp_template = IYC::get_dir() . '/' . Timber::$dirname . '/wp-templates/' . $template;
                break;
            }
        }

        $controller->render();

        return $wp_template;
    }
}