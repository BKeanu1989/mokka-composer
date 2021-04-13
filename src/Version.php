<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.3';

    public static function getVersion()
    {
        return self::VERSION;
    }
}