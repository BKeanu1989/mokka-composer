<?php

namespace Mokka\Exporter\Validator;
use Mokka\Exporter\Builder;

class Halle 
{
    protected $data;
    public function __construct(Builder $builder)
    {
        $this->data = $builder->getData();
        $this->errors = [];
    }

    public function is_valid()
    {
        $valid = false;
    }

    public function is_pre_order()
    {
        $is_pre_order = false;

        $pre_releases = array_filter(array_column($this->data, '_pre_release_date'));

        if (count($pre_releases) === 0) return false;

        foreach($pre_releases AS $single_pre_release) {
            $currentDate = new \DateTime();
            $preOrderDate = new \DateTime($single_pre_release);

            if($currentDate < $preOrderDate) {
                $is_pre_order = true;
                break;
            }
        }
        return $is_pre_order;
    }
}