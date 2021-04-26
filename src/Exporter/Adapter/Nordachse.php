<?php

namespace Mokka\Exporter\Adapter;

use Mokka\Interfaces\ExporterAdapterInterface;
use Mokka\Exporter\Adapter\HalleFormatter;

// orderId;creationdate;deliveryCompany;deliveryFirstname;deliveryLastname;deliveryStreet;deliveryZipcode;deliveryCity;deliveryCountry;orderItemId;qty;productSku;productName;currency;net_price;ODM;image;rohartikelnr;printStyle;parcel_id;DeliveryCareOf
class Nordachse  implements ExporterAdapterInterface

{
    public function __construct(\Mokka\Exporter\Builder $builder)
    {
        $this->data = $builder->getData();    
        $this->builder = $builder;
        $this->rows = [];
        $this->setup();
    }

    public function setup() 
    {
        $this->format();
    }

    public function setData(array $data)
    {

    }

    public function getData()
    {
        return $this->data;
    }

    
    public function format()
    {
        foreach($this->getData() AS $singleData) {
            extract($singleData);
            error_log("singleData");
            error_log(print_r($singleData, 1));
            $formatter = new HalleFormatter($singleData);
            $array = [
                'orderId' => '',
                'creationData' => $post_date,
                'deliveryCompany' => $formatter->deliveryCompany(),
                'deliveryFirstname' => $formatter->deliveryFirstname(),
                'deliveryLastname' => $formatter->deliveryLastname(),
                'deliveryStreet' => $formatter->deliveryStreet(),
                'deliveryZipcode' => $formatter->deliveryZipcode(),
                'deliveryCity' => $formatter->deliveryCity(),
                'deliveryCountry' => $formatter->deliveryCountry(),
                'orderItemId' => $formatter->getOrderItemId(),
                'qty' => $formatter->getQuantity(),
                'productSku' => $formatter->getProductSku(),
                'productName' => '',
                'currency' => $formatter->getCurrency(),
                'net_price' => $formatter->getPrice(),
                'ODM' => $formatter->getOdm(),
                'image' => '',
                'rohartikelnr' => '',
                'printStyle' => $formatter->getPrintStyle(),
                'parcel_id' => $formatter->getParcelId(),
                'DeliveryCareOf' => $formatter->DeliveryCareOf()
            ];

            $this->rows[] = $array;
        }
    }


    public function save()
    {

    }

    public function send()
    {

    }
}