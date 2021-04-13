<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.10';

    public static function getVersion()
    {
        return self::VERSION;
    }
}