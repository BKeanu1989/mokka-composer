<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.23';

    public static function getVersion()
    {
        return self::VERSION;
    }
}