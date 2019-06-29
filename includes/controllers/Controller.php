<?php
namespace IYC\controllers;

use Timber\Timber;
/**
 * Basically to help route to page/content templates
 * and to help set contexts
 */
class Controller 
{
    protected $template;
    protected $context = [];
    protected $post_id;
    protected $template_stack = [];

    public function __construct($template = '', $force_template = false) 
    {
        $this->context = get_context();
        $this->post_id = (get_the_ID()) ?: null;
        $this->template = $template;
        $this->set_template_stack($template);
        $this->set_content_template_stack();
        $this->init();
        /**
         * Unsets wp templates and puts template first if force_template is true,
         * useful if other teplates are loaded not by wp template_include hook
         * like sidebar.php
         */
        if ($force_template) {
            $this->template_stack = $template;
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
    public function set_template_stack($templates)
    {
        /**
         * Change the php extensions to .twig
         */
        $this->template_stack = $this->merge_helper($this->template_stack, $templates);
    }

    public function set_content_template_stack()
    {
        /**
         * adding content_stack context based on the template_stack
         */
        $this->context['content_stack'] = array_map(function($template) {
            return 'content-' . $template;
        }, $this->template_stack);
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

    public function add_context(array $context = [])
    {
        $this->context = array_merge($this->context, $context);
    }

    public function render()
    {
        $template_stack = apply_filters('pre_render_template_stack', $this->template_stack);

        if (!empty($this->template) && !is_array($this->template)) {
            $template_base = basename($this->template, '.twig');
        } else {
            foreach($template_stack as $template) {
                if (is_file(get_iyc_dir() . Timber::$dirname . '/' . $template)) {
                    $template_base = basename($template, '.twig');
                    break;
                } elseif ( is_file(locate_template(Timber::$dirname . '/' . $template)) ) {
                    $template_base = basename($template, '.twig');
                    break;
                }
            }
        }

        $context = apply_filters('pre_render_context', $this->context);

        /**
         * TODO: Doesn't check for php templates
         */
        if (isset($template_base)) {
            $context = apply_filters("pre_render_context_{$template_base}", $context);
        }

        render($template_stack, $context);
    }
}