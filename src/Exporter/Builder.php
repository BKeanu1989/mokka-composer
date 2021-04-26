<?php

namespace Mokka\Exporter;

use Mokka\Utils\WpHelper;

class Builder 
{
    protected $data;
    protected $order;

    public function __construct(\WC_Order $order)
    {
        $this->order = $order;

        $this->init();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function init() 
    {
        global $wpdb;

        $data = [];
        $items = $this->order->get_items();    
        $postData = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts WHERE ID = %d", $this->order->get_id()), ARRAY_A);
        $orderData = WpHelper::get_post_meta($this->order->get_id(), [
            '_billing_company',
            '_billing_first_name',
            '_billing_last_name',
            '_billing_email',
            '_billing_phone',
            '_billing_address_1',
            '_billing_address_1_5',
            '_billing_address_2',
            '_billing_postcode',
            '_billing_country',
            '_billing_city',
            '_mokka-click-collect',
            '_order_currency',
            '_shipping_first_name',
            '_shipping_last_name',
            '_shipping_address_1',
            '_shipping_address_1_5',
            '_shipping_address_2',
            '_shipping_city',
            '_shipping_postcode',
            '_shipping_company',
            '_shipping_country',
            '_parcel_id'
        ]);

        foreach ($items as $item) {

            $order_item_id = $item->get_id();
            // unset($itemData['meta_data']);
            $order_item_meta = WpHelper::get_order_item_meta($order_item_id, [
                '_product_id',
                '_variation_id',
                '_qty',
                '_line_subtotal'
            ]);
            $product_id = $order_item_meta['_product_id'];
            $variation_id = $order_item_meta['_variation_id'];

            $product_meta = WpHelper::get_post_meta($product_id, [
                "_motiv", 
                "_product_production", 
                "is_bundle_product",
                "_printStyle",
                '_artist_id'
            ]);

            $variation_meta = WpHelper::get_post_meta($variation_id, [
                "_sku",
                "_rohartikel_default",
                '_pre_release_date'
            ]);

            $name = [
                'name' => $item->get_name()
            ];

            $singleData = array_merge($product_meta, $variation_meta, $order_item_meta, $name, $postData, $orderData, ['order_item_id' => $order_item_id]);

            $data[] = $singleData;
        }

        // $data = array_merge($data, $orderData);

        $this->setData($data);
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this->data;
    }

    public function getData()
    {
        return $this->data;
    }
}