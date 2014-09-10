<?php
/**
 * @author     Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright  Copyright (c) 2014 Dmitriy Tyurin
 */

namespace Fobia\Helper;

/**
 * PHP callback encapsulation.
 *
 * @package Fobia.Helper
 */
final class Callback
{

    /** @var callable */
    private $cb;

    /**
     * Factory. Workaround for missing (new Callback)->invoke() in PHP 5.3.
     *
     * @param  mixed   class, object, callable
     * @param  string  method
     * @return \Fobia\Helper\Callback
     */
    public static function create($callback, $m = null)
    {
        return new self($callback, $m);
    }

    /**
     * @internal
     * @param  mixed   class, object, callable
     * @param  string  method
     */
    public function __construct($cb, $m = null)
    {
        if ($m !== null) {
            $cb = array($cb, $m);
        } elseif ($cb instanceof self) { // prevents wrapping itself
            $this->cb = $cb->cb;
            return;
        }

        if ( ! is_callable($cb, true)) {
            throw new \InvalidArgumentException("Invalid callback.");
        }
        $this->cb = $cb;
    }

    /**
     * Invokes callback. Do not call directly.
     *
     * @return mixed
     */
    public function __invoke()
    {
        if ( ! is_callable($this->cb)) {
            throw new \InvalidStateException("Callback '$this' is not callable.");
        }
        $args = func_get_args();
        return call_user_func_array($this->cb, $args);
    }

    /**
     * Invokes callback.
     *
     * @return mixed
     */
    public function invoke()
    {
        if ( ! is_callable($this->cb)) {
            throw new \InvalidStateException("Callback '$this' is not callable.");
        }
        $args = func_get_args();
        return call_user_func_array($this->cb, $args);
    }

    /**
     * Verifies that callback can be called.
     *
     * @return bool
     */
    public function isCallable()
    {
        return is_callable($this->cb);
    }

    /**
     * @internal
     * @return string
     */
    public function __toString()
    {
        if ($this->cb instanceof \Closure) {
            return '{closure}';
        } elseif (is_string($this->cb) && $this->cb[0] === "\0") {
            return '{lambda}';
        } else {
            is_callable($this->cb, true, $textual);
            return $textual;
        }
    }
}
