<?php

namespace Mokka\Utils;

class Logger 
{
    public function __construct()
    {
        
    }

    public static function write_log( $log ) 
    {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }

    public static function test() 
    {
        echo "update worked";
    }
}