<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.9';

    public static function getVersion()
    {
        return self::VERSION;
    }
}