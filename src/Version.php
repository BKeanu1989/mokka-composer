<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.2.1';

    public static function getVersion()
    {
        return self::VERSION;
    }
}