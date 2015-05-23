<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Dmitriy Tyurin

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.*/
/**
 * bootstrap.php~ class  - bootstrap.php~.php file
 *
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2015 Dmitriy Tyurin
 */

if (defined('TEST_BOOTSTRAP_FILE')) {
    return;
}
define('TEST_BOOTSTRAP_FILE', true);


$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    throw new RuntimeException('Установите зависимости для запуска PHPUnit.');
}
require_once $autoloadFile;
unset($autoloadFile);

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4("Fobia\\Helper\\", 'tests/Test');
$loader->register();

// TODO: check include path INCLUDE_PATH%
// ini_set('include_path', ini_get('include_path'));

// put your code here