<?php
namespace IYC;

class Context 
{
    public static function get_context() 
    {
        return [
            'body_class' => get_body_class(),
            'component' => new components\Components()
        ];
    }
}