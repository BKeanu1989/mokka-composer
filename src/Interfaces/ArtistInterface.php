<?php

namespace Mokka\Interfaces;

Interface ArtistInterface 
{
    public function __construct(int $user_id = 0);
    public function getData();
    public function setData();
}