<?php

namespace Mokka\Exporter\Adapter;
use Mokka\Utils\WpHelper;

class HalleFormatter
{
    /**
     * raw data
     *
     * @param array $data
     */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getOrderId()
    {
        return $this->data['ID'];
    }

    public function getOrderItemId() 
    {
        return $this->data['ID'] . $this->data['order_item_id'];
    }

    public function getOdm() 
    {
        $data = $this->data;

        $odm = (in_array($data["_product_production"], ['sieb', 'bandcamp'])) ? '' : 'x';
        return $odm;
    }

    public function getPrintStyle() 
    {
        $productProduction = $this->data['_product_production'];

        switch ($productProduction) {
            case 'sieb': // change: unbedruckt
                // translateToZero because of implemented validation check. value cant be falsy aka '0'
                return 'translateToZero';
            case 'onDemand': // change: DTG
                return '2';
            case 'sublimation':
                return '32';
            default:
                return '2';
        }
    }
    public function getParcelId() 
    {
        return $this->data['_parcel_id'];
    }

    public function deliveryCompany()
    {
        $data = $this->data;
        $company_to_use = WpHelper::return_last_truthy($data['_billing_company'], $data['_shipping_company']);
        if (!$company_to_use) {
            return '';
        }
        return WpHelper::replaceUmlauts($company_to_use);
    }

    public function deliveryFirstname()
    {
        $data = $this->data;

        $first_name_to_use = WpHelper::return_last_truthy($data['_billing_first_name'], $data['_shipping_first_name']);
        return WpHelper::replaceUmlauts($first_name_to_use);

    }

    public function deliveryLastname()
    {
        $data = $this->data;

        $last_name_to_use = WpHelper::return_last_truthy($data['_billing_last_name'], $data['_shipping_last_name']);
        return WpHelper::replaceUmlauts($last_name_to_use);

    }

    public function deliveryStreet()
    {
        $data = $this->data;
        $street_to_use = WpHelper::return_last_truthy($data['_billing_address_1'], $data['_shipping_address_1']);
        $street = WpHelper::replaceUmlauts($street_to_use);

        $street_number_to_use = WpHelper::return_last_truthy($data['_billing_address_1_5'], $data['_shipping_address_1_5']);
        return $street_to_use . ' ' . $street_number_to_use;
    }

    public function deliveryZipcode()
    {
        $data = $this->data;

        $zip_code_to_use = WpHelper::return_last_truthy($data['_billing_postcode'], $data['_shipping_postcode']);
        return WpHelper::replaceUmlauts($zip_code_to_use);

    }

    public function deliveryCity()
    {
        $data = $this->data;

        $city_to_use = WpHelper::return_last_truthy($data['_billing_city'], $data['_shipping_city']);
        return WpHelper::replaceUmlauts($city_to_use);
    }

    public function deliveryCareOf()
    {
        $data = $this->data;
        $string = WpHelper::return_last_truthy($data['_billing_address_2'], $data['_shipping_address_2']);
        if (!$string) {
            return '';
        }
        return WpHelper::replaceUmlauts($string);
    }

    public function deliveryCountry()
    {
        $data = $this->data;

        $string = WpHelper::return_last_truthy($data['_billing_country'], $data['_shipping_country']);
        return $string;
    }

    public function get_product_hs_code() {
        $data = '61091000';
        
        $filtered_data = apply_filters(WP_Helpers::get_filter_name($this->name, __FUNCTION__), $data);
        return $filtered_data;
    }
    
    public function get_product_country_of_manufacture() {
        $data = 'DE';

        $filtered_data = apply_filters(WP_Helpers::get_filter_name($this->name, __FUNCTION__), $data);
        return $filtered_data;
    }

    public function getProductSku()
    {
        $data = $this->data;
        $istLagerProdukt = $data["_product_production"] === 'sieb';
        $product_sku = ($istLagerProdukt) ? $data["_rohartikel_default"] : $data["_sku"];

        return $product_sku;
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getPrice()
    {
        return number_format((float) $this->data["_line_subtotal"], 2, '.', '');
    }

    public function getQuantity()
    {
        return $this->data['_qty'];
    }

    public function getCurrency()
    {
        return $this->data['_order_currency'];
    }

    public function getRohartikel()
    {
        return $this->data['_rohartikel_default'];
    }

    public function getImage()
    {
        $image_link = (!empty($this->data['_mokka-click-collect'])) ? 1 : $this->data['_motiv'];
        return $image_link;
    }

    public function getCreationDate()
    {
        return $this->data['post_date'];
    }
}