<?php
/**
 * test.php file
 *
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2014 Dmitriy Tyurin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Fobia\Helpers as fh;

$arr = array(1,2,3,4);

fh\print_pre($arr);

$a = fh\am($arr, 1,2,3,4);
print_r($a);

$arr = array(
    'k1' => 1,
    'k2' => 2,
    'k3' => 3,
    'k4' => 4,
);
$r = fh\ak($arr, 'k4', array('k2', 'k3'));


print_r($r);

fh\dump($r);
\Fobia\Helpers\VarDumper::dump($r);
