<?php

namespace Mokka\Bac;

abstract class Manager 
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getPositiveTransfers()
    {
        $rows = [];
        foreach($this->data AS $row) {
            $reader = new FidorReader($row);
            $amount = $reader->getAmount();

            if ($amount > 0) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}