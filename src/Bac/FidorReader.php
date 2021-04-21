<?php

namespace Mokka\Bac;
use Mokka\Bac\Reader;


class FidorReader extends Reader
{
    protected $amount;
    protected $name = [];
    protected $orderId;

    public function __construct($row = [])
    {
        $this->row = $row;
        $this->init();
    }

    public function init()
    {
        $this->amount = $this->setAmount($this->row);
        $this->name = $this->setName($this->row);
        $this->orderId = $this->setOrderId($this->row);
    }

    public function setAmount($singleDataSet, $column = 'Wert') 
    {
        return  floatval(str_replace(',', '.', str_replace('.', '', $singleDataSet[$column])));
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setName($singleDataSet, $column = 'Beschreibung2') 
    {
        $absender =  $singleDataSet[$column];
        preg_match('/Absender: (.*)(. IBAN)/', $absender, $absender_output);

        if (count($absender_output) > 0) {
            $name = $absender_output[1];
            $name = str_replace(","," ", $name);
            $name_array = explode(" ", $name);
            return $name_array;
        }
        
        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setOrderId($singleDataSet, $column = 'Beschreibung') 
    {
        $purpose_string = $singleDataSet[$column];
        // preg_match('/-([\d]+)/', $purpose_string, $order_id);
        // if (!empty($order_id[1])) {
        //     return $order_id[1];
        // } 

        // try to get number with 5+ digits
        preg_match('/ \d{5,}/', $purpose_string, $order_id);
        if(count($order_id) > 0) {
            return intval($order_id[0]);
        }

    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}