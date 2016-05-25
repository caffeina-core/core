<?php

/**
 * Module trait
 *
 * Provides a way to extend static classes with new methods.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015/2016 - http://caffeina.com
 */

trait Module {
    static protected $__PROTOTYPE = array();

    final public function __call($name, $args){
      if (isset(static::$__PROTOTYPE[$name]) && static::$__PROTOTYPE[$name] instanceof \Closure)
        return call_user_func_array(static::$__PROTOTYPE[$name]->bindTo($this, $this), $args);
      if (get_parent_class())
        return parent::__call($name, $args);
      else throw new \BadMethodCallException;
    }

    final public static function __callStatic($name, $args){
      if (isset(static::$__PROTOTYPE[$name]) && static::$__PROTOTYPE[$name] instanceof \Closure)
        return forward_static_call_array(static::$__PROTOTYPE[$name], $args);
      if (get_parent_class()) return parent::__callStatic($name, $args);
      else throw new \BadMethodCallException;
    }

    public static function extend($methods = []){
      if ($methods) foreach ($methods as $name => $method) {
        if ($method && $method instanceof \Closure)
          static::$__PROTOTYPE[$name] = $method;
        else throw new \BadMethodCallException;
      }
    }

}
