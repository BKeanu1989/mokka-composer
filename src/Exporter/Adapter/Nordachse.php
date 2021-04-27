<?php

namespace Mokka\Exporter\Adapter;

use Mokka\Interfaces\ExporterAdapterInterface;
use Mokka\Exporter\Adapter\HalleFormatter;
use Mokka\Exporter\Adapter\Halle;

// orderId;creationdate;deliveryCompany;deliveryFirstname;deliveryLastname;deliveryStreet;deliveryZipcode;deliveryCity;deliveryCountry;orderItemId;qty;productSku;productName;currency;net_price;ODM;image;rohartikelnr;printStyle;parcel_id;DeliveryCareOf
class Nordachse extends Halle implements ExporterAdapterInterface

{
    public function __construct(\Mokka\Exporter\Builder $builder)
    {
        $this->data = $builder->getData();    
        $this->builder = $builder;
        $this->rows = [];
        $this->setup();
    }

    // public function setup() 
    // {
    //     $this->format();
    // }

    // public function setData(array $data)
    // {

    // }

    // public function getData()
    // {
    //     return $this->data;
    // }

    
    // public function format()
    // {
    //     foreach($this->getData() AS $singleData) {
    //         $formatter = new HalleFormatter($singleData);
    //         $array = [
    //             'orderId' => $formatter->getOrderId(),
    //             'creationData' => $formatter->getCreationDate(),
    //             'deliveryCompany' => $formatter->deliveryCompany(),
    //             'deliveryFirstname' => $formatter->deliveryFirstname(),
    //             'deliveryLastname' => $formatter->deliveryLastname(),
    //             'deliveryStreet' => $formatter->deliveryStreet(),
    //             'deliveryZipcode' => $formatter->deliveryZipcode(),
    //             'deliveryCity' => $formatter->deliveryCity(),
    //             'deliveryCountry' => $formatter->deliveryCountry(),
    //             'orderItemId' => $formatter->getOrderItemId(),
    //             'qty' => $formatter->getQuantity(),
    //             'productSku' => $formatter->getProductSku(),
    //             'productName' => $formatter->getName(),
    //             'currency' => $formatter->getCurrency(),
    //             'net_price' => $formatter->getPrice(),
    //             'ODM' => $formatter->getOdm(),
    //             'image' => $formatter->getImage(),
    //             'rohartikelnr' => $formatter->getRohartikel(),
    //             'printStyle' => $formatter->getPrintStyle(),
    //             'parcel_id' => $formatter->getParcelId(),
    //             'DeliveryCareOf' => $formatter->DeliveryCareOf()
    //         ];

    //         $this->rows[] = $array;
    //     }
    // }


    public function save()
    {

    }

    public function send()
    {

    }
}