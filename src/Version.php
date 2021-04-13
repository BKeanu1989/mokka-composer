<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.6';

    public static function getVersion()
    {
        return self::VERSION;
    }
}