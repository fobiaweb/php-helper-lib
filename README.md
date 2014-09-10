# php-helper-lib

[![Total Downloads](https://poser.pugx.org/fobiaweb/php-helper-lib/downloads.png)](https://packagist.org/packages/fobiaweb/php-helper-lib)
[![Latest Stable Version](https://poser.pugx.org/fobiaweb/php-helper-lib/v/stable.png)](https://packagist.org/packages/fobiaweb/php-helper-lib)



## Installation

Can be installed with [Composer](http://getcomposer.org)

```json
{
    "require": {
        "fobiaweb/php-helper-lib": "*"
    }
}
```

Please refer to [Composer's documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) for more detailed installation and usage instructions.


## Feature

#### Callback

PHP обратного вызова инкапсуляции.

    use Fobia\Helpers\Callback;

    $_callback = function($a, $b) {
        echo $a . " = " . $b . "\n";
    };
    $cb = Callback::create($_callback);
    $cb(1, 2);
    $cb->invoke(3, 4);
    //
    // Анологично и для методов объекта
    $cb = Callback::create($object, 'method');
    // или
    $cb = Callback::create(array($object, 'method'));




#### DateTime

Дата и время с сериализацией и временных меток поддержкой PHP 5.2.

    $date = new DateTime('2000-12-31');
    echo $date . "\n" ; // '2000-12-31 00:00:00'

    $date->modify('+1 month');
    echo $date->format('Y-m-d') . "\n"; // 2001-01-31

    $newDate = $date->modifyClone('+1 month');
    echo $date->format('Y-m-d') . "\n";     // 2001-01-31
    echo $newDate->format('Y-m-d') . "\n";  // 2001-03-03



#### JavaScript

Является вспомогательным классом, содержащий JavaScript-функции, связанные обработки.

    use Fobia\Helpers\JavaScript;

    $options = array('key1'=>true,'key2'=>123,'key3'=>'value');
    echo JavaScript::encode($options);
    // The following javascript code would be generated:
    // {"key1":true,"key2":123,"key3":"value"}


Этот код можно использовать в `JS`

    <script>
    var options = <?php echo JavaScript::encode($options); ?>;
    console.log(options);
    </script>


#### JSON

Предоставляет простой кодер и декодер для записи JSON. Аналогичен функциям 
`json_decode($json)` и `json_encode($value)`


#### VarDumper

Призван заменить функцию `var_dump` и `print_r`

Вывод на экран дамп переменой с подсветкой по умолчанию
    
    Fobia\Helpers\VarDumper::dump($var);

Вывод на экран дамп переменой с подсветкой классами в тегах

    Fobia\Helpers\VarDumper::dump($var, 10, 'class');

Строковое представления дампа переменой без подсветки

    $s = dumpAsString($var, 15);


