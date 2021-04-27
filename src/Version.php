<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.5.2';

    public static function getVersion()
    {
        return self::VERSION;
    }
}