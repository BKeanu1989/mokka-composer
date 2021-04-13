<?php

require_once __DIR__ . '/vendor/autoload.php';

$month = date('Y-m');

echo $month;

$foo = [22222,3333];

echo serialize($foo);