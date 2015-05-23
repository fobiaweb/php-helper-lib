<?php
/**
 * CVarDumper class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Fobia\Helper;

/**
 * VarDumper призван заменить функцию var_dump и print_r.
 *
 * Он может правильно определить рекурсивно ссылающееся объекты в сложном объекте структуры.
 * Она также имеет рекурсивный контроль глубины, чтобы избежать бесконечных рекурсий.
 *
 * VarDumper может быть использован следующим образом,
 * <code>
 * Fobia\Helper\VarDumper::dump($var);
 * Fobia\Helper\VarDumper::dump($var, 10, 'class');
 * $s = dumpAsString($var, 15);
 * </code>
 *
 * @package Fobia.Helper
 */
class VarDumper
{

    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Отображает переменную.
     * Этот метод позволяет достичь аналогичного функциональность как var_dump и print_r
     *
     * @param mixed $var                Переменная, подлежащих дампу
     * @param integer        $depth     Максимальная глубина, что даипер должен пройти в переменной.
     *                                  По умолчанию 10.
     * @param boolean|string $highlight Подсвечивать результат или установить класс тегам
     */
    public static function dump($var, $depth = 10, $highlight = true)
    {
        echo self::dumpAsString($var, $depth, $highlight);
    }

    /**
     * Парсирует в строку переменную.
     * Этот метод позволяет достичь аналогичного функциональность как var_dump и print_r
     *
     * @param mixed $var                Переменная, подлежащих дампу
     * @param integer        $depth     Максимальная глубина, что даипер должен пройти в переменной.
     *                                  По умолчанию 10.
     * @param boolean|string $highlight Подсвечивать результат или установить класс тегам
     * @return string Строковое представление переменной
     */
    public static function dumpAsString($var, $depth = 10, $highlight = false)
    {
        self::$_output = '';
        self::$_objects = array();
        self::$_depth = $depth;
        self::dumpInternal($var, 0);
        if ($highlight) {
            $result = highlight_string("<?php\n" . self::$_output, true);
            $pattern = array( '/&lt;\\?php<br \\/>/', '/<code>/' );
            $replace = array( '', '<code class="debug-vardumper">' );
            $result = preg_replace($pattern, $replace, $result);

            if ($highlight === 'class') {
                $pattern = array(
                    '/&lt;\\?php<br \\/>/',
                    '/<code>/',
                    '/style="color: ' .  ini_get('highlight.string')   .'"/',
                    '/style="color: ' .  ini_get('highlight.comment')  .'"/',
                    '/style="color: ' .  ini_get('highlight.keyword')  .'"/',
                    '/style="color: ' .  ini_get('highlight.bg')       .'"/',
                    '/style="color: ' .  ini_get('highlight.default')  .'"/',
                    '/style="color: ' .  ini_get('highlight.html')     .'"/',
                );
                $replace = array(
                    '',                                 // for <?php
                    '<code class="debug-vardumper">',  // for <code>
                    'class="string"',                   // style="color:
                    'class="comment"',  // #888A85;
                    'class="keyword"',
                    'class="bg"',
                    'class="default"',
                    'class="html"',
                );
                $result = preg_replace($pattern, $replace, $result);
            }
            self::$_output = $result;
        }
        return self::$_output;
    }

    /**
     * @param mixed $var variable to be dumped
     * @param integer $level depth level
     */
    private static function dumpInternal($var, $level)
    {
        switch (gettype($var)) {
            case 'boolean':
                self::$_output.=$var ? 'true' : 'false';
                break;
            case 'integer':
                self::$_output.="$var";
                break;
            case 'double':
                self::$_output.="$var";
                break;
            case 'string':
                self::$_output.="'" . addslashes($var) . "'";
                break;
            case 'resource':
                self::$_output.='{resource}';
                break;
            case 'NULL':
                self::$_output.="null";
                break;
            case 'unknown type':
                self::$_output.='{unknown}';
                break;
            case 'array':
                if (self::$_depth <= $level) {
                    self::$_output.='array(...)';
                } else if (empty($var)) {
                    self::$_output.='array()';
                } else {
                    $keys   = array_keys($var);
                    $spaces = str_repeat(' ', $level * 4);
                    self::$_output.="array\n" . $spaces . '(';
                    foreach ($keys as $key) {
                        $key2 = str_replace("'", "\\'", $key);
                        self::$_output.="\n" . $spaces . "    '$key2' => ";
                        self::$_output.=self::dumpInternal($var[$key],
                                                           $level + 1);
                    }
                    self::$_output.="\n" . $spaces . ')';
                }
                break;
            case 'object':
                if (($id = array_search($var, self::$_objects, true)) !== false) {
                    self::$_output.=get_class($var) . ' #' . ($id + 1) . '(...)';
                } else if (self::$_depth <= $level) {
                    self::$_output.=get_class($var) . '(...)';
                } else {
                    $id        = array_push(self::$_objects, $var);
                    $className = get_class($var);
                    //if ($var instanceof IDump) {
                    //    $members = $var->dump();
                    //} else {
                    $members   = (array) $var;
                    //}
                    $spaces    = str_repeat(' ', $level * 4);
                    self::$_output.="$className #$id\n" . $spaces . '(';
                    foreach ($members as $key => $value) {
                        $keyDisplay = strtr(trim($key), array("\0" => ':'));
                        self::$_output.="\n" . $spaces . "    [$keyDisplay] => ";
                        self::$_output.=self::dumpInternal($value, $level + 1);
                    }
                    self::$_output.="\n" . $spaces . ')';
                }
                break;
        }
    }
}