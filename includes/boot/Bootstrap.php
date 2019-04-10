<?php
namespace IYC\boot;

/**
 * Class to help with loading
 */
class Bootstrap 
{
    public function __construct() {}

    /**
     * Loads directory files named after classes and instantiates them
     * 
     * created: 4/10/19
     * updated: TBD
     * 
     * @param string $dir_path        dir path
     * @param string $ext             file extension to search
     * @param string $namespace       namespace of class
     * @param array  $ignored_classes classes to not instantiate
     */
    public function directory_class_loader($dir_path, $ext, $namespace = '', $ignored_classes = []) 
    {
        $files = glob($dir_path . '*' . $ext);

        foreach ((array)$files as $file) {
            $class = basename($file, $ext);

            if (!in_array($class, $ignored_classes)) {
                new $namespace . $class;
            }
        }
    }
}