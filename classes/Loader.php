<?php

/**
 * Loader
 *
 * Easy class autoloading.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Loader {
    protected static $paths = [];

    /**
     * Adds a path to class autoloader
     * @param string $path The path root to add to class autoloader
     * @param string $name An optional name for path section
     */
    public static function addPath($path,$name=null){
        static::$paths[$path] = $name;
    }

    /**
     * Register core autoloader
     * @return bool Returns false if autoloader failed inclusion
     */
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

// Automatically register core classes.
Loader::addPath(__DIR__);
Loader::register();
