<?php

/**
 * Event
 *
 * Generic event emitter-listener.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Event {
    use Module;

    protected static $_listeners = [];

    public static function on($name,callable $listener){
        static::$_listeners[$name][] = $listener;
    }

    public static function single($name,callable $listener){
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

    public static function trigger($name){
        if (false === empty(static::$_listeners[$name])){
            $args = func_get_args();
            array_shift($args);
            $results = [];
            foreach (static::$_listeners[$name] as $listener) {
                $results[] = call_user_func_array($listener,$args);
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
