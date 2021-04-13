<?php

namespace Mokka\Reports;
use Mokka\Utils\WpArtist;
use Mokka\Utils\Logger;
use Mokka\Utils\WpHelper;
use Mokka\Utils\BasePrice;
use Mokka\Reports\Order;

class WpOrder extends Order 
{
    protected $whiteList = [];
    protected $offset = 0;
    protected $length = 100;
    protected $data;
    public function __construct(array $whiteList, int $offset = 0, int $length = 100)
    {
        $this->whiteList = $whiteList;
        $this->offset = $offset;
        $this->length = $length;

        $this->init();

    }

    protected function init()
    {
        $baseDir = wp_upload_dir()['basedir'] . '/mokka-misc/';
        $month = date('Y-m');

        $path = $baseDir . $month;
        wp_mkdir_p($path);

    }

    /**
     * getOrderIdsByArtist
     * 
     * @param int $artist_number
     * @return array
     */
    public static function getOrderIdsByArtist($artist_number) 
    {
        return $this->orderIds;
    }

    public function setOrderIds(array $data) {
        $this->orderIds = $data;
        return $this->orderIds;
    }

    public function setOrderIdsByArtist($artist_number)
    {
        
        global $wpdb;
        $productIds = WpArtist::get_all_product_ids_by_artist_number($artist_number);

        $in_array_sql = implode("', '", $productIds);
        $query = "SELECT ITEMS.order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta AS ITEMMETA JOIN {$wpdb->prefix}woocommerce_order_items AS ITEMS ON ITEMMETA.order_item_id = ITEMS.order_item_id WHERE meta_key = '_product_id' AND meta_value IN ('$in_array_sql')";

        $results = $wpdb->get_col($query);
        $this->orderIds = $results;
        return $results;

    }

    public function build()
    {
        global $wpdb;
        $orderIds = array_slice($this->orderIds, $this->offset, $this->length);

        $buil = [];

        foreach($orderIds AS $orderId) {
            $_order = wc_get_order($orderId);
            if (!$_order) continue;
            $items = $_order->get_items();
            $orderId = $_order->get_id();
            $_order_data = [
                'status' => $_order->get_status(),
                'order_id' => $orderId
            ];
            // var_dump($items);
            foreach($items AS $item) {
                $singleData = $item->get_data();
                $_product = $item->get_product();

                $_product_id = $singleData['product_id'];
                $_variation_id = $singleData['variation_id'];

                $basePrice = new BasePrice($_product->get_id());

                $productWhiteList = $this->whiteList['product'];
                $variationWhiteList = $this->whiteList['variation'];
                $in_array_product = implode("', '", $productWhiteList);
                $in_array_variation = implode("', '", $variationWhiteList);
                $product_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array_product') AND post_id = $_product_id", ARRAY_A);
                $variation_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array_variation') AND post_id = $_variation_id", ARRAY_A);

                $item_data = [
                    'name' => $item->get_name(),
                    'quantity' => $singleData['quantity'],
                    'total_tax_excluded' => $singleData['total'],
                    'tax' => $singleData['total_tax'],
                    'base_price' => $basePrice->get_price()
                ];



                $combined = array_merge(WpHelper::pullOut($product_data, $productWhiteList), WpHelper::pullOut($variation_data, $variationWhiteList), $_order_data, $item_data);
                $built[$orderId] = $combined;
            }
        }
        $this->data = $built;
        return $built;
    }

    public function save($fileName)
    {
        try {
            $dir = wp_upload_dir()['basedir'] . '/mokka-misc/';
            $test = wp_upload_dir();
            $fileName = $fileName;
            $fileExt = '.csv';
    
            $fh = fopen("{$dir}{$fileName}{$fileExt}", 'w');
            
            $first_array_key = array_key_first($this->data);
            $header_row = array_keys($this->data[$first_array_key]);
            fputcsv( $fh, $header_row, ',');
    
            foreach ($this->data as $fields) {
                fputcsv($fh, $fields, ',');
            }
            
            fclose($fh);
            $this->saved_file = "{$dir}{$fileName}{$fileExt}";
        } catch (\Throwable $th) {
            //throw $th;
            error_log("no items for bill csv");
        }
    }

    
}