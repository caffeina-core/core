<?php

/**
 * Cache
 *
 * Multi-strategy cache store.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Cache {
   protected static $driver = null;

    public static function get($key,$default='',$expire=0){
        $hash = static::hash($key);
        if(static::$driver->exists($hash) && $results = static::$driver->get($hash)){
            return $results;
        } else {
            if($data = is_callable($default)?call_user_func($default):$default){
                static::$driver->set($hash,$data,$expire);
            }
            return $data;
        }
    }

    /**
     * Load cache drivers with a FCFS strategy
     *
     * @method using
     * @param  mixed $driver can be a single driver name string, an array of driver names or a map [ driver_name => driver_options array ]
     * @return bool   true if a driver was loaded
     * @example
     *   
     *   Cache::using('redis');
     *   Cache::using(['redis','files','memory']); // Prefer "redis" over "files" over "memory" caching
     *   Cache::using([
     *         'redis' => [
     *             'host'   => '127.0.0.1',
     *             'prefix' => 'mycache',
     *          ],
     *         'files' => [
     *             'cache_dir' => '/tmp',
     *         ],
     *         'memory'
     *   ]);
     * 
     */
    public static function using($driver){
      foreach((array)$driver as $key => $value){
          if(is_numeric($key)){
            $drv = $value;
            $conf = [];
          } else {
            $drv = $key;
            $conf = $value;
          }
          $class = 'Cache\\' . ucfirst(strtolower($drv));
          if(class_exists($class) && $class::valid()) {
            static::$driver = new $class($conf);
            return true;
          }
        }
       return false;
    }

    public static function set($key,$value,$expire=0){
        return static::$driver->set(static::hash($key),$value,$expire);
    }

    public static function delete($key){
        return static::$driver->delete(static::hash($key));
    }

    public static function exists($key){
        return static::$driver->exists(static::hash($key));
    }

    public static function flush(){
        return static::$driver->flush();
    }

    public static function inc($key,$value=1){
        return static::$driver->inc(static::hash($key),$value);
    }

    public static function dec($key,$value=1){
        return static::$driver->dec(static::hash($key),$value);
    }

    public static function hash($key,$group=null){
        static $hashes = [];
        if (false === isset($hashes[$group][$key])){
            $k = $key;
            if(is_array($key) && count($key)>1) list($group,$key) = $k;
            $hashes[$group][$key] = ($group?$group.'-':'') . md5($key);
        }
        return $hashes[$group][$key];
    }
}

Cache::using('memory');
