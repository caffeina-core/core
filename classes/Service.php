<?php

/**
 * Service Module
 *
 * This module permits the user to register and retrieve a service manager
 * instance, one (singleton) or multiple times
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Service {
    use Module;
    private static $services = [];

    public static function register($serviceName, $serviceFactory){
      static::$services[$serviceName] = function() use ($serviceName, $serviceFactory) {
        return static::$services[$serviceName] = call_user_func_array($serviceFactory, func_get_args());
      };
    }

    public static function registerFactory($serviceName, $serviceFactory){
        static::$services[$serviceName] = function() use ($serviceName, $serviceFactory) {
            return call_user_func_array($serviceFactory, func_get_args());
        };
    }

    public static function __callStatic($serviceName, $serviceParameters){
    	return empty(static::$services[$serviceName])
                   ? null
                   : (is_callable(static::$services[$serviceName])
                       ? call_user_func_array( static::$services[$serviceName], $serviceParameters)
                       : static::$services[$serviceName]
                   );
    }


}
