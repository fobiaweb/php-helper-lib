<?php
/**
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2014 Dmitriy Tyurin
 */

namespace Fobia\Helpers;

/**
 * DateTime with serialization and timestamp support for PHP 5.2.
 *
 * @package Fobia.Helpers
 */
class DateTime extends \DateTime
{

    /** minute in seconds */
    const MINUTE = 60;

    /** hour in seconds */
    const HOUR = 3600;

    /** day in seconds */
    const DAY = 86400;

    /** week in seconds */
    const WEEK = 604800;

    /** average month in seconds */
    const MONTH = 2629800;

    /** average year in seconds */
    const YEAR = 31557600;
    const ATOM = 'Y-m-d H:i:s';

    /**
     * DateTime object factory.
     *
     * <code>
     * // Дата/время во временной зоне Вашего компьютера.
     * $date = new DateTime('2000-01-01');
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2000-01-01 00:00:00-05:00
     *
     * // Дата/время в заданной временной зоне.
     * $date = new DateTime('2000-01-01', new DateTimeZone('Pacific/Nauru'));
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2000-01-01 00:00:00+12:00
     *
     * // Текущие дата и время во временной зоне Вашего компьютера.
     * $date = new DateTime();
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2010-04-24 10:24:16-04:00
     *
     * // Текущие дата и время в заданной временной зоне.
     * $date = new DateTime(null, new DateTimeZone('Pacific/Nauru'));
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2010-04-25 02:24:16+12:00
     *
     * // Использование метки времени UNIX.
     * // Обратите внимание: результат во временной зоне UTC.
     * $date = new DateTime('@946684800');
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2000-01-01 00:00:00+00:00
     *
     * // Несуществующие значения все равно обрабатываются.
     * $date = new DateTime('2000-02-30');
     * echo $date->format('Y-m-d H:i:sP') . "\n"; // 2000-03-01 00:00:00-05:00
     *
     *
     * </cedo>
     *
     * @param  string|int|\DateTime
     * @return DateTime
     */
    public static function from($time)
    {
        if ($time instanceof \DateTime) {
            return new self($time->format('Y-m-d H:i:s'), $time->getTimezone());
        } elseif (is_numeric($time)) {
            if ($time <= self::YEAR) {
                $time += time();
            }
            return new static(date('Y-m-d H:i:s', $time));
        } else { // textual or NULL
            return new static($time);
        }
    }

    /**
     * Строковый Формат даты.
     *
     * @return string
     */
    public function toString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     *  Изменение временной метки.
     *
     * <code>
     * $date = new DateTime('2000-12-31');
     *
     * $date->modify('+1 month');
     * echo $date->format('Y-m-d') . "\n"; // 2001-01-31
     *
     * $date->modify('+1 month');
     * echo $date->format('Y-m-d') . "\n"; // 2001-03-03
     * </code>
     *
     * @param string $modify  Строка даты/времени. Объяснение корректных форматов дано в {@see http://ru2.php.net/manual/ru/datetime.formats.php}.
     * @return self
     */
    public function modifyClone($modify = '')
    {
        $dolly = clone $this;
        return $modify ? $dolly->modify($modify) : $dolly;
    }

    /**
     * @internal
     */
    public function __toString()
    {
        return $this->toString();
    }
}
