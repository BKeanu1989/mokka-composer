<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.2.8';

    public static function getVersion()
    {
        return self::VERSION;
    }
}