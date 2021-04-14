<?php

namespace Mokka;

class Version 
{
    CONST VERSION = '0.3.18';

    public static function getVersion()
    {
        return self::VERSION;
    }
}