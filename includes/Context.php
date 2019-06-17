<?php
namespace IYC;

use IYC\components\Components;

class Context 
{
    public static function get_context() 
    {
        return [
            'body_class' => get_body_class(),
            'component' => new Components()
        ];
    }
}