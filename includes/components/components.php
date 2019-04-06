<?php
namespace IYC\components;

class Components 
{
    public function __construct() {}
    /**
     * Pass in a name of a component class, with context/args
     */

    public function __call($name, $arguments) 
    {
        $namespaces = [__NAMESPACE__];
        
        /**
         * Let's not init the main class
         */
        if (strtolower($name) === 'component')
            return;

        foreach ($namespaces as $namespace) {
            $class = $namespace . '\\' . $name;
            if (class_exists($class)) {
                $class = new $class($arguments);
                $class->render();
            }
        }
    }
}