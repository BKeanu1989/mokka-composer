<?php

namespace Mokka\Utils\Metas;

class Order 
{
    public static $metas = [
        ''
    ];

    const METAS = [
        'local_pickup_forced' => '_force_local_pickup',
        'local_pickup_enabled' => '_enable_local_pickup',
        'parcel_id' => '_parcel_id',
        'tracking_id' => '_tracking_id',
        'parcel' => [
            'post' => 19,
            'dhl' => 11,
            'key' => '_parcel_id'
        ]
    ];

    public function __construct()
    {
        
    }


}