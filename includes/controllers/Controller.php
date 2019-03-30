<?php
namespace IYC\controllers;

use Timber\Timber;
/**
 * Basically to help route to page/content templates
 * and to help set contexts
 */
class Controller 
{
    protected $template = [];
    protected static $wp_template = '';
    protected $context = [];
    protected $post_id;
    protected $template_stack = [];
    protected static $extension = '.twig';

    public function __construct(string $template, $force_template = false) 
    {
        $this->context = get_context();
        $this->post_id = (get_the_ID()) ?: null;
        $this->template = $template;
        $this->set_template_stack();
        $this->init();
        $this->set_context_template_stack();
        /**
         * Unsets wp templates and puts template first if force_template is true,
         * useful if other teplates are loaded not by wp template_include hook
         * like sidebar.php
         */
        if ($force_template) {
            $this->template_stack = $template;
            self::$wp_template = $template;
        }
    }
    
    /**
     * Initializes class after everything is set
     */
    public function init() {} //override this method
    public function remove_template($needle, array $stack)
    {
        if (($key = array_search($needle, $stack)) !== false) {
            unset($this->stack[$key]);
        }
    }
    /**
     * Sets WP file first in template stack for twig 
     * to load by default if not force_template
     * otherwise it will load what the default template 
     * wordpress is looking for. This basically helps for
     * custom page templates.
     */
    public function set_template_stack()
    {
        $this->template_stack[] = self::$wp_template;
        $this->template_stack = $this->merge_helper($this->template_stack, $this->template);
    }
    public function set_context_template_stack()
    {
        $this->context['templates'] = [
            'parts/content-' . self::$wp_template,
            'parts/content-' . get_post_type(),
            'parts/content-' . $this->template,
            'parts/content.twig' //if all else fails let's load content.twig as a default
        ];
    }
    public function add_template($template)
    {
        $this->template = $this->merge_helper($this->template, $template);
    }
    /**
     * Helps merge non arrays to arrays
     */
    public function merge_helper(array $arr_to_merge, $arr_to_check)
    {
        if (is_array($arr_to_check)) {
            $arr_to_merge = array_merge($arr_to_merge, $arr_to_check);
        } else {
            $arr_to_merge[] = $arr_to_check;
        }
        return $arr_to_merge;
    }
    /**
     * This allows us to hook into wordpress core standards
     */
    public static function set_wp_template($template)
    {
        self::$wp_template = basename($template, '.php') . self::$extension;
    }
    public function add_context(array $context = [])
    {
        $this->context = array_merge($this->context, $context);
    }
    public function render()
    {
        $template_base = basename($this->template, '.twig');

        $template_stack = apply_filters('pre_render_template_stack', $this->template_stack);
        $context = apply_filters('pre_render_context', $this->context);
        $context = apply_filters("pre_render_context_{$template_base}", $context);
        render($template_stack, $context);
    }
}