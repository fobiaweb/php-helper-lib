<?php
/**
 * Библиотека основный функций
 *
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2014 Dmitriy Tyurin
 */

namespace Fobia\Helper;

if (defined('FOBIA_COMMON_FILE')) {
    return;
}
define('FOBIA_COMMON_FILE', true);


// Падавление ошибок
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
//trigger_error($error_msg)
//error_log();

// Автокодировка
// mb_internal_encoding('UTF-8');


/* * **********************************************
 * CONSTANTS
 * ********************************************** */
// Начальное время
if (!$_SERVER["REQUEST_TIME_FLOAT"]) {
    $_SERVER["REQUEST_TIME_FLOAT"] = microtime(true);
}
if (!defined("TIME_START")) {
    define("TIME_START", $_SERVER["REQUEST_TIME_FLOAT"]);
}
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('BR') or define('BR', '<br />' . PHP_EOL);

/** Указывае что скрипт был запущен через консоль */
defined('IS_CLI') or define('IS_CLI', !isset($_SERVER['HTTP_HOST']));
defined('REMOTE_SERVER') or define('REMOTE_SERVER', !IS_CLI
        && $_SERVER['REMOTE_ADDR'] != '127.0.0.1'
        && $_SERVER['REMOTE_ADDR'] != '192.168.33.1');

/* * **********************************************
 * ENVIRONMENT
 * ********************************************** */

// Функция вызываемая при вызове dump()
if (!isset($_ENV['dump.callback'])) {
    $_ENV['dump.callback'] = function($var) {
        if (@constant('REMOTE_SERVER') && REMOTE_SERVER) {
            VarDumper::dump($var, 10, true);
        } else {
            var_dump($var);
        }
    };
}

// if (!isset($_ENV['logger'])) {
//     $_ENV['logger'] = new \Monolog\Logger('CORE');
// }
/* * **********************************************
 * FUNCTIONS
 * ********************************************** */

/**
 * Загрузить файл конфигурации из директории CONFIG_DIR либо абсолютного пути
 * (php, ini, json, yml)
 *
 * @param string $file
 * @return mixed
 */
function loadConfig($file, $type = null)
{
    $type = $type ? $type : strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if ( ! is_readable($file)) {
        trigger_error("Не найден конфигурационый файла '{$file}'.", E_USER_ERROR);
        return false;
    }

    $data = false;
    switch ($type) {
        case 'php':
            $data = include $file;
            break;
        case 'json':
            $data = \Fobia\Helper\JSON::decode(file_get_contents($file));
            break;
        case 'ini':
        case 'conf':
            $data = parse_ini_file($file);
            break;
        case 'xml':
            $data = json_decode(json_encode(simplexml_load_file($file)), true);
            break;
        case 'yml':
            if (function_exists('yaml_parse_file')) {
                $data = yaml_parse_file($file);
            } else {
                $data = \Symfony\Component\Yaml\Yaml::parse($file);
            }
            break;
        case 'cache':
            $data = @unserialize(file_get_contents($file));
            break;
        default :
            trigger_error("Неизвестный формат конфигурационого файла '$type'.",
                          E_USER_ERROR);
            break;
    }

    return $data;
}

/**
 * Print var in teg pre
 *
 * @param mixed $var
 */
function print_pre($var)
{
    echo "<pre>\n" . print_r($var, true) . "\n</pre>";
}

/**
 * dump var
 *
 * @see $_ENV['dump.callback']
 * @param mixed $value
 * @param mixed $value2...
 *
 * @return void
 */
function dump($value)
{
    if (is_callable($_ENV["dump.callback"])) {
        $args = func_get_args();
        foreach ($args as $value) {
            call_user_func($_ENV["dump.callback"], $value);
        }
    }
}

/**
 * Сортировка масива $array по ключу $sortby.
 *
 * @param array $array Array to sort
 * @param string $sortby Sort by this key
 * @param string $order  Sort order asc/desc (ascending or descending).
 * @param integer $type Type of sorting to perform
 * @return mixed Sorted array
 */
function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC)
{
    if (!is_array($array)) {
        return null;
    }
    foreach ($array as $key => $val) {
        $sa[$key] = $val[$sortby];
    }
    if ($order == 'asc') {
        asort($sa, $type);
    } else {
        arsort($sa, $type);
    }
    foreach ($sa as $key => $val) {
        $out[] = $array[$key];
    }
    return $out;
}


/**
 * Сортировк масива $array по ключу $sortby в порядке, как перечислено $keys.
 *
 * @param array  $array  исходный масив объектов или масивов
 * @param string $sortby ключ сравнения
 * @param mixed  $keys   перечисления значений, чей порядок приоритетный
 * @param mixed  ...
 * @return mixed
 */
function sortByIndex(&$array, $sortby, $keys)
{
    if (!is_array($array)) {
        return null;
    }
    if (!is_array($keys)) {
        $keys = array($keys);
    }

    $n = func_num_args();
    if ($n > 3) {
        for ($i = 3; $i < $n; $i ++) {
            $k = func_get_arg($i);
            if (is_array($k)) {
                $keys = am($keys, $k);
            } else {
                $keys[] = $k;
            }
        }
    }
    $keys = array_unique($keys);

    $out = array();
    while (count($array) && count($keys)) {
        $k = array_shift($keys);
        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $_k = $val->$sortby;
            } else {
                $_k = $val[$sortby];
            }

            if ($_k == $k) {
                $out[] = $val;
                unset($array[$key]);
                break;
            }
        }
    }
    return am($out, $array);
}

/**
 * Смерживание масивов
 *
 * @param array $a First array
 * @param array $... Second array
 * @return array Все параметры массива объединены в один
 */
function am($a)
{
    $r    = array();
    $args = func_get_args();
    foreach ($args as $a) {
        if (!is_array($a)) {
            $a = array($a);
        }
        $r = array_merge($r, $a);
    }
    return $r;
}

/**
 * Выбрать из масива определенные ключи
 *
 * @param array        $array   исходный масив
 * @param array|string $keys    First key
 * @param array|string ...      Second key...
 * @return array
 */
function ak(array $array, $keys)
{
    $keys = array();

    $args = func_get_args();
    array_shift($args);
    foreach ($args as $k) {
       $keys = am($keys, $k);
    }
    $keys = array_unique($keys);

    return array_intersect_key($array, array_flip($keys));
}

/**
 * Преобразует в масив полей
 *
 * @param array|string $var1
 * @param array|string $...
 * @return array
 */
function parseFields()
{
    $array = array();
    foreach (func_get_args() as $var) {
        if (!is_array($var)) {
            $var = explode(',', $var);
        }
        $array = am($array, $var);
    }
    array_walk($array, function(&$val) {
        $val = trim($val);
    });
    $array = array_unique($array);
    return array_values($array);
}

/**
 * Преобразует список id, переданные масивом или строкой в масив.
 * Возвращает только целые числа без повторений.
 *
 * @param string|array $var
 * @return array
 */
function parseNumbers()
{
    $array = array();
    foreach (func_get_args() as $var) {
        if (!is_array($var)) {
            $var = explode(',', $var);
        }
        $array = am($array, $var);
    }

    array_unshift($array, null);
    array_walk($array, function(&$v)  {
        $v = (is_numeric($v)) ? (int) $v : null;
    });
    $array = array_unique($array);
    array_shift($array);
    return array_values($array);
}

/**
 * Преобразует в целое
 *
 * @param int|string $value
 * @return int
 */
function parseInt($value)
{
    return (int) $value;
}

/**
 * Преобразует в вещественное
 *
 * @param float|string $value
 * @return float
 */
function parseFloat($value)
{
    return (float) $value;
}

/**
 * Преобразует в целое, положительное
 *
 * @param int|string $value
 * @return int
 */
function parsePositive($value)
{
    $value = (int) $value;
    return ($value >= 0) ? $value : 0;
}


/**
 * Являеться ли масив списком
 *
 * @param array $array
 * @return boolean
 */
function isList(array $array)
{
    foreach (array_keys($array) as $k => $v) {
        if ($k !== $v) {
            return false;
        }
    }
    return true;
}

/**
 * Поиск файла во всех директориях PATH
 *
 * @param string $file File to look for
 * @return string полный путь к файлу, если существует, в противном случае false
 */
function fileExistsInPath($file, $paths = null)
{
    if (file_exists($file)) {
        return $file;
    }
    $paths = (is_array($paths)) ? $paths : explode(PATH_SEPARATOR, ini_get('include_path'));
    foreach ($paths as $path) {
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fullPath)) {
            return $fullPath;
        }
    }
    return false;
}

/**
 * Функция-оболочка для call_user_func_array ( почти в два раза быстрее )
 * Если вы вызываете метод с известным числом параметров, быстрее будет это таким образом:
 * <code>
 *   $class->{$method}($param1, $param2);
 *      VS
 *   call_user_func_array (array($class, $method), array($param1, $param2));
 * </code>
 * Но если вы не знаете, сколько параметров ...
 *
 * Функция-оболочка ниже немного быстрее, но проблема сейчас в том, что вы
 * делаете два вызова функции. Один к обертке и один к функцию.
 *
 * @param string $o object
 * @param string $method method
 * @param array  $p arguments
 */
function dispatchMethod($o, $method, array $p = null)
{
    switch (@count($p)) {
        case 0: return $o->{$method}();
        case 1: return $o->{$method}($p[0]);
        case 2: return $o->{$method}($p[0], $p[1]);
        case 3: return $o->{$method}($p[0], $p[1], $p[2]);
        case 4: return $o->{$method}($p[0], $p[1], $p[2], $p[3]);
        case 5: return $o->{$method}($p[0], $p[1], $p[2], $p[3], $p[4]);
        default: return call_user_func_array(array($o, $method), $p);
    }
}
