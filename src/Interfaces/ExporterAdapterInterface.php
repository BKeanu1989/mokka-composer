<?php

namespace Mokka\Interfaces;
use Mokka\Exporter\Builder;

Interface ExporterAdapterInterface 
{
    public function __construct(Builder $data);
    public function setData(array $data);
    public function getData();
    public function format();
    
    public function save();
    public function send();
}