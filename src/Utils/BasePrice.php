<?php

namespace Mokka\Utils;
use Mokka\Utils\WpHelper;

class BasePrice 
{
    /**
     * Undocumented function
     *
     * @param integer $variation_id
     * @param integer $product_id
     */
    public function __construct($product_id) {

        $this->name = get_class($this);
        $this->product_id = $product_id;
        $this->product = wc_get_product($product_id);

        $this->base_price_to_use = 0;
        $this->base_price_infos = [];

        $this->init();
        $this->evaluate_base_price_to_use();
    }

    public function init() {
        $rohartikel_basic_price_info = $this->get_rohartikel_basic_price_info($this->product);
        $product_basic_price_info = $this->get_product_basic_price_info($this->product);
        $variation_basic_price_info = $this->get_variation_basic_price_info($this->product);

        $this->push_single_base_price_info($rohartikel_basic_price_info);
        $this->push_single_base_price_info($product_basic_price_info);
        $this->push_single_base_price_info($variation_basic_price_info);
    }

    public function push_single_base_price_info($info) {
        $this->base_price_infos[] = $info;
    }

    public function evaluate_base_price_to_use() {
        $base_price_infos = apply_filters(WpHelper::get_filter_name($this->name, __FUNCTION__), $this->base_price_infos, $base_info = $this->get_base_info(), $this->product_id);

        $infos_with_price = array_filter($base_price_infos, function($object) {
            $price = $object->price;

            if ($price != 0) {
                return true;
            }
            // return ($object->price !== 0);
        });

        usort($infos_with_price, function($object, $nextObject) {
            if ($object->quantifier === $nextObject->quantifier) {
                return 0;
            }
            return ($object->quantifier > $nextObject->quantifier) ? -1 : 1;
        });

        $this->base_price_to_use = isset($infos_with_price[0]) ? $infos_with_price[0]->price: $this->get_price();
        return $this->base_price_to_use;
    }

    public function get_rohartikel_basic_price_info($product) {
        global $wpdb;
        $quantifier = 10;
        $level = 'rohartikel';

        $is_variation = WpHelper::is_variation($product);
        $info = $this->get_base_info($quantifier, $level);

        if ($is_variation) {
            $variation_id = $product->get_id();
            $rohartikel_nr_extern = get_post_meta($variation_id, '_rohartikel_default', true);
            if (!empty($rohartikel_nr_extern)) {
                $query = $wpdb->prepare("SELECT basis_preis FROM {$wpdb->prefix}rohartikel WHERE rohartikel_nr_extern = %s", $rohartikel_nr_extern);
                $basic_price = $wpdb->get_var($query);
                $info->price = (float) $basic_price;
            }
        }

        return $info;
    }

    public function get_product_basic_price_info($product) {
        $level = 'product';
        $quantifier = 20;

        $is_variation = WpHelper::is_variation($product);
        $info = $this->get_base_info($quantifier, $level);

        if (!$is_variation) {
            $id = $product->get_id();
        } else {
            $id = $product->get_parent_id();
        }

        $product_basic_price = get_post_meta($id, '_base_price_product', true);

        $info->price = (float) $product_basic_price;

        return $info;
    }
    
    public function get_variation_basic_price_info($product) {
        $level = 'variation';
        $quantifier = 30;

        $is_variation = WpHelper::is_variation($product);
        $info = $this->get_base_info($quantifier, $level);

        if ($is_variation) {
            $variation_id = $product->get_id();
            $variation_basic_price = get_post_meta($variation_id, '_base_price_variation', true);

            $info->price = (float) $variation_basic_price;            
        }

        return $info;
    }
    
    public function is_variation($product) {
        try {
            if (gettype($product) === 'boolean') return false;
            
            $is_variation = ($product->get_type() === 'variation') ? true : false;
            return $is_variation;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }

    }

    public function get_base_info($quantifier = 0, $level = '') {
        $base_info = (object) [
            'price' => 0,
            'quantifier' => $quantifier,
            'level' => $level
        ];

        return $base_info;
    }

    public function get_price() {
        return $this->base_price_to_use;
    }
}