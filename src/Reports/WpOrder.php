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
    protected $orderIds;
    protected $orderStatusToFilterBy;
    protected $saved_file;
    
    public function __construct(array $whiteList, int $offset = 0, int $length = 100, $filterByOrderStatus = [])
    {
        $this->whiteList = $whiteList;
        $this->offset = $offset;
        $this->length = $length;

        $this->init();
        $this->orderStatusToFilterBy = $filterByOrderStatus;
    }

    protected function init()
    {
        $baseDir = wp_upload_dir()['basedir'] . '/mokka-misc/';
        $month = date('Y-m');

        $path = $baseDir . $month;
        wp_mkdir_p($path);

    }

    public function setOrderIds(array $data) {
        $this->orderIds = $data;
        return $this->orderIds;
    }
    
    public function maybeFilterOrderStatus() 
    {
        if (is_array($this->orderStatusToFilterBy) && count($this->orderStatusToFilterBy) > 0) {
            $newOrderIds = $this->filterByOrderStatus();
            $this->setOrderIds($newOrderIds);
            return $newOrderIds;
        }
    }

    public function setOrderIdsByArtist($artist_number)
    {
        
        global $wpdb;
        $productIds = WpArtist::get_all_product_ids_by_artist_number($artist_number);

        $in_array_sql = implode("', '", $productIds);
        $query = "SELECT ITEMS.order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta AS ITEMMETA JOIN {$wpdb->prefix}woocommerce_order_items AS ITEMS ON ITEMMETA.order_item_id = ITEMS.order_item_id WHERE meta_key = '_product_id' AND meta_value IN ('$in_array_sql')";

        $results = $wpdb->get_col($query);
        $this->setOrderIds($results);
        return $results;
    }

    public function setOrderIdsByProducts(array $productIds) 
    {
        global $wpdb;

        $in_array_sql = implode("', '", $productIds);
        $query = "SELECT ITEMS.order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta AS ITEMMETA JOIN {$wpdb->prefix}woocommerce_order_items AS ITEMS ON ITEMMETA.order_item_id = ITEMS.order_item_id WHERE meta_key = '_product_id' AND meta_value IN ('$in_array_sql')";

        $results = $wpdb->get_col($query);
        $this->setOrderIds($results);
        return $results;
    }

    public function setOrderIdsByTimeFrame($start, $end)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE date(post_date) BETWEEN %s AND %s AND post_type = 'shop_order'", $start, $end);
        $orderIds = $wpdb->get_col($query);

        $this->setOrderIds($orderIds);
        return $orderIds;
    }

    public function filterByOrderStatus() 
    {
        global $wpdb;

        $oldOrderIds = $this->orderIds;
        $in_array_orderIds = implode("', '", $oldOrderIds);
        $in_array_order_status = implode("', '", $this->orderStatusToFilterBy);
        $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE ID IN ('$in_array_orderIds') AND post_status IN ('$in_array_order_status')";
        $newOrderIds = $wpdb->get_col($query);

        return $newOrderIds;
    }


    public function build()
    {
        global $wpdb;
        Logger::write_log("order ids before slice");
        Logger::write_log($this->orderIds);
        $orderIds = array_slice($this->orderIds, $this->offset, $this->length);

        Logger::write_log("order ids after slice");
        Logger::write_log($orderIds);
        $built = [];

        foreach($orderIds AS $orderId) {
            $_order = wc_get_order($orderId);
            if (!$_order) continue;
            $items = $_order->get_items();
            $orderId = $_order->get_id();
            $_post = get_post($orderId);
            $_order_data = [
                'status' => $_order->get_status(),
                'order_id' => $orderId,
                'post_date' => $_post->post_date
            ];
            // var_dump($items);
            foreach($items AS $item) {
                $singleData = $item->get_data();
                $_product = $item->get_product();

                $_product_id = $singleData['product_id'];
                $_variation_id = $singleData['variation_id'];

                $basePrice = new BasePrice($_product->get_id());

                $productWhiteList = $this->whiteList['product'];
                $in_array_product = implode("', '", $productWhiteList);
                $product_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array_product') AND post_id = $_product_id", ARRAY_A);

                $variationWhiteList = $this->whiteList['variation'];
                $in_array_variation = implode("', '", $variationWhiteList);
                $variation_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array_variation') AND post_id = $_variation_id", ARRAY_A);

                $orderWhiteList = $this->whiteList['order'];
                $in_array_order = implode("', '", $orderWhiteList);
                $order_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('$in_array_order') AND post_id = $orderId", ARRAY_A);
                

                $item_data = [
                    'name' => $item->get_name(),
                    'quantity' => $singleData['quantity'],
                    'total_tax_excluded' => $singleData['total'],
                    'tax' => $singleData['total_tax'],
                    'base_price' => $basePrice->get_price()
                ];

                $combined = array_merge($_order_data, WpHelper::pullOut($order_data, $orderWhiteList), WpHelper::pullOut($product_data, $productWhiteList), WpHelper::pullOut($variation_data, $variationWhiteList), $item_data);
                $built[] = $combined;
            }
        }
        $this->data = $built;
        return $built;
    }

    public function getOrderIdsCount()
    {
        return count($this->orderIds);
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

    public function download() {
        if(file_exists($this->saved_file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($this->saved_file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($this->saved_file));
            flush(); // Flush system output buffer
            readfile($this->saved_file);
            exit;
        }
    }

    
}