<?php

namespace Mokka\Utils;

class Logger 
{
    public function __construct()
    {
        
    }

    public static function write_log( $message ) 
    {
        $path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../') . '/logs/log.log';
        error_log("---PATH---");
        error_log($path);
        $myfile = fopen($path, "a") or die("Unable to open file!");
        $dateNow = date("Y-m-d H:i:s");
        $now= new \DateTime("@" . strtotime($dateNow));
        $now->modify("+2 hours");
        $now = $now->format("Y-m-d H:i:s");

        if ( is_array( $message ) || is_object( $message ) ) {
            $message = print_r( $message, true );
        }

        $message ='[' . $now . ']: ' . $message . "\n";
        fwrite($myfile, $message);
        fclose($myfile);
    }

    public static function test() 
    {
        echo "some more fake changes ";
    }

    
}