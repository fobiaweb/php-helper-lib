<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Fobia\Helpers\DateTime;

$date = new DateTime('2000-12-31');
echo $date . "\n" ; // '2000-12-31 00:00:00'

$date->modify('+1 month');
echo $date->format('Y-m-d') . "\n"; // 2001-01-31

$newDate = $date->modifyClone('+1 month');
echo $date->format('Y-m-d') . "\n";     // 2001-01-31
echo $newDate->format('Y-m-d') . "\n";  // 2001-03-03

