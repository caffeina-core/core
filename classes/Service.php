<?php

/**
 * Dictionary class
 *
 * The dictionary class allow to handle a repository of key-values data
 * Values are accessibles via a dot notation key path.
 *
 * Example:
 * <code>
 *  class MyConfig extends Dictionary {}
 *  MyConfig::set('user',[ 'name' => 'Frank', 'surname' => 'Castle' ]);
 *  echo "Hello, my name is ",MyConfig::get('user.name'),' ',MyConfig::get('user.surname');
 * </code>
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Service {
    use Module;
    private static $services = [];

    public function single($serviceName, $serviceFactory){
      static::$services[$serviceName] = function() use ($serviceName, $serviceFactory) {
        return static::$services[$serviceName] = call_user_func_array($serviceFactory, func_get_args());
      };
    }

    public function multiple($serviceName, $serviceFactory){
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
