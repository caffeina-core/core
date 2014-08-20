<?php

/**
 * Module trait
 *
 * Provides a way to extend static classes with new methods.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

trait Module {
    static protected $__classMethods = array();
    
    public function __call($name, $args){
        if (isset(static::$__classMethods[$name]) && static::$__classMethods[$name] instanceof \Closure) {
            return call_user_func_array(static::$__classMethods[$name]->bindTo($this, $this), $args);
        }
        
        if (get_parent_class()) {
            return parent::__call($name, $args);
        }
        
        throw new \BadMethodCallException;
    }
    
    public static function __prototypeAdd($name, \Closure $method){
        static::$__classMethods[$name] = $method;
    }

    public static function __prototypeGet($name){
        return isset(static::$__classMethods[$name])?static::$__classMethods[$name]:null;
    }
    
    public static function __prototypeRemove($name){
        unset(static::$__classMethods[$name]);
    }
    
    public static function __callStatic($name, $args){
        if (isset(static::$__classMethods[$name]) && static::$__classMethods[$name] instanceof \Closure) {
            return forward_static_call_array(static::$__classMethods[$name], $args);
        }
        
        if (get_parent_class()) {
            return parent::__call($name, $args);
        }
        
        throw new \BadMethodCallException;
    }
    
    public static function extend($methodMap=[]){
        if ($methodMap) foreach ($methodMap as $name => $method) {
            if($method && $method instanceof \Closure) {
                static::__prototypeAdd($name,$method);
            } else throw new \BadMethodCallException;
        }
    }
}
