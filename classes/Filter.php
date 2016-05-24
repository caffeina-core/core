<?php

/**
 * Filter
 *
 * Permits users to override data via callback hooks.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2016 - http://caffeina.com
 */

class Filter {
    use Module;

    protected static $_modders = [];

    public static function add($names, callable $modder = null){
      if( null === $modder ) foreach ( (array)$names as $name => $callback ) {
        static::$_modders[$name][] = $callback;
      } else foreach ( (array)$names as $name ) {
        static::$_modders[$name][] = $modder;
      }
    }

    public static function single($name,callable $modder){
      static::$_modders[$name] = [$modder];
    }

    public static function remove($name,callable $modder = null){
      if($modder === null) {
        unset(static::$_modders[$name]);
      } else {
        if ($idx = array_search($modder,static::$_modders[$name],true))
          unset(static::$_modders[$name][$idx]);
      }
    }

    public static function with($names, $default){
      foreach ((array)$names as $name) {
        if (!empty(static::$_modders[$name])) {
          $value = $default;
          $args  = array_slice( func_get_args(), 2 );
          foreach (static::$_modders[$name] as $modder) {
            $value = call_user_func( $modder,$value,$args );
          }
          return $value;
        }
      }
      return $default;
    }

}
