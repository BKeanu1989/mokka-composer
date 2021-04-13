<?php

namespace Mokka\Reports;
use Mokka\Utils\WpArtist;
use Mokka\Utils\Logger;

abstract class Order 
{
    protected $orderIds;
    public function __construct()
    {
        
    }

    /**
     * getOrderIdsByArtist
     * 
     * @param int $artist_number
     * @return array
     */
    public static function getOrderIdsByArtist($artist_number) 
    {
        // global $wpdb;
        // $productIds = WpArtist::get_all_product_ids_by_artist_number($artist_number);

        // $in_array_sql = implode("', '", $productIds);
        // $query = "SELECT ITEMS.order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta AS ITEMMETA JOIN {$wpdb->prefix}woocommerce_order_items AS ITEMS ON ITEMMETA.order_item_id = ITEMS.order_item_id WHERE meta_key = '_product_id' AND meta_value IN ('$in_array_sql')";

        // $results = $wpdb->get_col($query);

        // return $results;
    }


}