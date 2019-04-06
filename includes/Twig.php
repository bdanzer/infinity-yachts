<?php
namespace IYC;

class Twig 
{
    public function __construct() 
    {
        add_filter('timber/twig', [$this, 'add_to_twig']);
    }

    public function add_to_twig($twig) 
    {
        /* this is where you can add your own functions to twig */
        $twig->addExtension(new \Twig_Extension_StringLoader());
        $twig->addFilter(new \Twig_SimpleFilter('uppercase', [$this, 'uppercase']));
        return $twig;
    }

    public function uppercase($text) 
    {
        return ucwords($text);
    }
}