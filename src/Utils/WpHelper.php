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

    public static function get_post_meta(int $post_id, array $whiteList = [])
    {
        global $wpdb;

        $in_array = implode("', '", $whiteList);
        $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array') AND post_id = $post_id", ARRAY_A);

        return self::pullOut($data, $whiteList);
        
    }

    public static function get_order_item_meta(int $id, array $whiteList)
    {
        global $wpdb;

        $in_array = implode("', '", $whiteList);
        $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key IN ('$in_array') AND order_item_id = $id", ARRAY_A);

        return self::pullOut($data, $whiteList);
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

    public static function redirect_to_same_page()
    {
        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * load_template
     * 
     * @param string $filepath
     * @param array $args
     */
    public function load_template($filepath, $args) {
        global $wp_version;
    
        // if template supports args argument
        if (version_compare($wp_version, '5.5.0', '>=')) {
            if ( $overridden_template = locate_template( $filepath ) ) {
                /*
                 * locate_template() returns path to file.
                 * if either the child theme or the parent theme have overridden the template.
                 */
                load_template( $overridden_template, false, $args );
            } else {
                /*
                 * If neither the child nor parent theme have overridden the template,
                 * we load the template from the 'templates' sub-directory of the directory this file is in.
                 */
                load_template( $filepath, false, $args );
            }
        } else {
            if ( $overridden_template = locate_template( $filepath ) ) {
                /*
                 * locate_template() returns path to file.
                 * if either the child theme or the parent theme have overridden the template.
                 */
                set_query_var('args', $args);
                load_template( $overridden_template, false);
            } else {
                /*
                 * If neither the child nor parent theme have overridden the template,
                 * we load the template from the 'templates' sub-directory of the directory this file is in.
                 */
                set_query_var('args', $args);
                load_template($filepath);
            }
        }
    }

    public static function return_last_truthy(...$params) {
        $params_length = count($params);
        for ($i=$params_length; $i > 0; $i--) { 
            $key = $i - 1;
            $is_empty = $params[$key] === ''; 
    
            if ($is_empty || !$params[$key]) {
                continue;
            }
            return $params[$key];
        }
    }
}