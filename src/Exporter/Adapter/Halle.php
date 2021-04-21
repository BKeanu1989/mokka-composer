<?php

namespace Mokka\Exporter\Adapter;

use Mokka\Interfaces\ExporterAdapterInterface;

class Halle implements ExporterAdapterInterface
{
    // orderId;creationdate;deliveryCompany;deliveryFirstname;deliveryLastname;deliveryStreet;deliveryZipcode;deliveryCity;deliveryCountry;orderItemId;qty;productSku;productName;currency;net_price;ODM;image;rohartikelnr;printStyle;parcel_id;DeliveryCareOf
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
            $array = [
                'orderId' => '',
                'creationData' => $post_date,
                'deliveryCompany' => ''
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

    public function create_file()
    {
        
    }
}