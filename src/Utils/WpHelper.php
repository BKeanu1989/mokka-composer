<?php

namespace Mokka\Utils;

class WpHelper 
{
    public function __construct()
    {
        
    }

    /**
     * $name is the name of the class. || O
     * $function is the function which called it.
     * 
     * View it like that:
     * - CLASS NAME
     * -- FUNCTION NAME
     * 
     */
    public static function get_filter_name($name, $function) {
        $str = '';

        $str = $name .'_'. $function;
        return $str;
    }
}