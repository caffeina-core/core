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
    static protected $__PROTOTYPE__ = array();

    final public function __call($name, $args){
      if (isset(static::$__PROTOTYPE__[$name]) && static::$__PROTOTYPE__[$name] instanceof \Closure)
        return call_user_func_array(static::$__PROTOTYPE__[$name]->bindTo($this, $this), $args);
      if (get_parent_class())
        return parent::__call($name, $args);
      else throw new \BadMethodCallException;
    }

    final public static function __callStatic($name, $args){
      if (isset(static::$__PROTOTYPE__[$name]) && static::$__PROTOTYPE__[$name] instanceof \Closure)
        return forward_static_call_array(static::$__PROTOTYPE__[$name], $args);
      if (get_parent_class()) return parent::__callStatic($name, $args);
      else throw new \BadMethodCallException;
    }

    public static function extend($method, $callback=null){
      $methods = ($callback === null && is_array($method)) ? $method : [$method=>$callback];
      foreach ($methods as $name => $meth) {
        if ($meth && $meth instanceof \Closure)
          static::$__PROTOTYPE__[$name] = $meth;
        else throw new \BadMethodCallException;
      }
    }

}
