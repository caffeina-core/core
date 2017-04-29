<?php

/**
 * Events trait
 *
 * Add to a class for a generic, private event emitter-listener.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2016 - http://caffeina.com
 */

namespace Core;

trait Events {

    protected static $_listeners = [];

    public static function on($name,callable $listener){
        static::$_listeners[$name][] = $listener;
    }

    public static function onSingle($name,callable $listener){
        static::$_listeners[$name] = [$listener];
    }

    public static function off($name,callable $listener = null){
        if($listener === null) {
            unset(static::$_listeners[$name]);
        } else {
            if ($idx = array_search($listener,static::$_listeners[$name],true))
                unset(static::$_listeners[$name][$idx]);
        }
    }

    public static function alias($source,$alias){
        static::$_listeners[$alias] =& static::$_listeners[$source];
    }

    public static function trigger($name, ...$args){
        if (false === empty(static::$_listeners[$name])){
            $results = [];
            foreach (static::$_listeners[$name] as $listener) {
                $results[] = $listener(...$args);
            }
            return $results;
        };
    }

    public static function triggerOnce($name){
        $res = static::trigger($name);
        unset(static::$_listeners[$name]);
        return $res;
    }

}
