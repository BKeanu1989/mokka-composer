<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.28';

    public static function getVersion()
    {
        return self::VERSION;
    }
}