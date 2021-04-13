<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.2.4';

    public static function getVersion()
    {
        return self::VERSION;
    }
}