<?php
/**
 * File
 *
 * Filesystem utilities.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class File {
	use Module;

    protected static $mount_points = [];

    public static function mount($alias, $driver, array $options = []) {
        $driver_class = '\\File\\'.ucfirst(strtolower($driver));
        if (!class_exists($driver_class)) throw new \Exception('Filesystem driver '.$driver.' not found.');
        static::$mount_points[$alias] = new $driver_class($options);
    }
    
    public static function unmount($alias) {
       unset(static::$mount_points[$alias]); 
    }

    public static function mounts() {
       return array_keys(static::$mount_points); 
    }

    public static function __callStatic($name, $params) {
        $uri = array_shift($params);
        if ($file_location = static::locate($uri)){
            list($mount, $path) = $file_location;
            array_unshift($params, static::resolvePath($path));
            if (empty(static::$mount_points[$mount])) return false;
            return call_user_func_array([static::$mount_points[$mount],$name],$params);           
        } else return false;
    }

    public static function locate($path) {
        if (strpos($path,'://')!==false) {
            list($mount, $filepath) = explode('://',$path,2);
            $filepath = static::resolvePath($filepath);
            return isset(static::$mount_points[$mount]) ? [$mount, $filepath] : false;
        } else {
            $path = static::resolvePath($path);
            foreach(static::$mount_points as $mount => $fs){
                if ($fs->exists($path)) return [$mount, $path];
            }
            return false;
        }
    }
    
    public static function resolvePath($path) {
        $path = str_replace(['/', '\\'], '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return trim(implode('/', $absolutes),'/');
    }    

    public static function search($pattern, $recursive=true){
        $results = [];
        foreach (static::$mount_points as $mount => $fs) {
            foreach($fs->search($pattern, $recursive) as $path) {
                $results[] = $mount.'://'.$path;
            }
        }
        return $results;
    }

    
}


interface FileInterface {
    
    public function exists($path);
    public function read($path);
    public function write($path, $data);
    public function append($path, $data);
    public function search($pattern, $recursive=true);
        
}
