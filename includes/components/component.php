<?php
namespace IYC\components;

class Component
{   
    protected $context = [];
    protected $file;
    protected $render = true;

    public function __construct($args)
    {
        /**
         * Set context with the global context helper
         */
        $this->context = get_context();
        /**
         * If args are empty set proper array
         */
        if (empty($args)) {
            $args = [
                []
            ];
        }
        $args = $this->parse_allowed_values($args);
        $this->set_defaults($args);
        $this->parse_class_context($args);
        $this->init($args);
    }

    public function init() {} //override method

    /**
     * Global values
     */
    public function global_values() 
    {
        return [
            'post' => false
        ];
    }
    /**
     * Should be named after timber model methods such as title, content
     * if they can come from a post object. If the value isn't set these
     * values will be used instead.
     */
    public function default_values() 
    {
        return [
            '' => ''
        ];
    }

    /**
     * If value/arg isn't set then use default value
     */
    public function set_defaults($args) 
    {
        $this->context = $this->context + wp_parse_args($args[0], $this->default_values());
    }

    public function add_context($context) 
    {
        $this->context = array_merge($this->context, $context);
    }

    /**
     * Only allow defaults set in components to be used
     */
    public function parse_allowed_values($args)
    {
        $default_args = $this->global_values() + $this->default_values();
        foreach($args[0] as $key => $value) {
            if (!array_key_exists($key, $default_args))
                unset($args[0][$key]);
        }
        return $args;
    }

    /**
     * Loop through the component array key values and check if 
     * it exists as a method in the class context
     */
    public function parse_class_context($args) 
    {
        if (!isset($args[0]['post']) || !is_object($args[0]['post']))
            return;
        $class = $args[0]['post'];
        if (!get_class($class)) {
            unset($this->context['post']);
            return;
        }
        /**
         * Continue to past post data if is post
         */
        if ('publish' == get_post_status($args[0]['post']->ID)) {
            $this->context['post'] = $args[0]['post'];
        }
        
        /**
         * Loop through each default_value and check the 
         * key/method in the Timber class passed. If found
         * execute and add to context data.
         */
        foreach ($this->default_values() as $key => $value) {
            if (method_exists($class, $key) || property_exists($class, $key)) {
                $class->$key();
                $this->context[$key] = $class->$key();
            }
        }
    }

    /**
     * Do stuff before render, can be overwritten
     */
    public function pre_render() {}

    /**
     * Don't override this method, set $this->render = false
     * or use pre/post_render()
     */
    public function render()
    {
        $this->pre_render();
        //There are good reasons to not render at all if some conditions don't meet
        if ($this->render) {
            render($this->file, $this->context);
        }
        $this->post_render();
    }

    /**
     * Do stuff after render, can be ovewritten
     */
    public function post_render() {}
}