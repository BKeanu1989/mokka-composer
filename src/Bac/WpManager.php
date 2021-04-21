<?php

namespace Mokka\Bac;
use Mokka\Bac\Manager;
use Mokka\Bac\FidorReader;

class WpManager extends Manager 
{
    protected $data;
    public function __construct($data)
    {
        parent::__construct($data);
        $this->data;
    }

    // public function getPositiveTransfers()
    // {
    //     $rows = [];
    //     foreach($this->data AS $row) {
    //         $reader = new FidorReader($row);
    //         $amount = $reader->getAmount();

    //         if ($amount > 0) {
    //             $rows[] = $row;
    //         }
    //     }

    //     return $rows;
    // }
}