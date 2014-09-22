<?php
/**
 * Loader
 *
 * Easy class autoloading.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Loader {
    
    protected static $paths = [];
    
    public static function addPath($path,$name=null){
        static::$paths[$path] = $name;
    }

    public static function register(){
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(function($class){
            $cfile = strtr($class,'_\\','//') . '.php';
            foreach (static::$paths as $path => $v) {
                $file = rtrim($path,'/').'/'.$cfile;
                if(is_file($file)) return include($file);
            }
            return false;
        },false,true);
    }
    
}

Loader::addPath(__DIR__);
Loader::register();