<?php

namespace Mokka\Bac\Interfaces;

interface ReaderInterface 
{
    public function getAmount($singleDataSet);
    public function getName($singleDataSet);
    public function getOrderId($singleDataSet);
}