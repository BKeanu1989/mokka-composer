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

    /**
     * Pulls only a given set out of an assosiative array.
     * 
     * $data - whatever associative array is pulled from db.
     * $whiteListed - the whiteList. Or to say it in non offending way to some social justice warriors. The list which u want.
     * $lookFor =   
     * 
     * @since 0.3.0
     * 
     */
    public static function pullOut($data, array $whiteListed = [], string $lookFor = 'meta_value', string $getValue = 'meta_key') {
        $returnArray = [];
        $formatted_data = array_column($data, $lookFor, $getValue);
    
        foreach($whiteListed AS $key) {
            if (isset($formatted_data[$key])) {
                $returnArray[$key] = $formatted_data[$key];
                continue;
            }
            $returnArray[$key] = '';
        }
        return $returnArray;
    }

    /**
     * is_variation
     * 
     * @param WC_Product
     * @return bool
     */
    public static function is_variation($product) {
        try {
            if (gettype($product) === 'boolean') return false;
            
            $is_variation = ($product->get_type() === 'variation') ? true : false;
            return $is_variation;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }

    }
}