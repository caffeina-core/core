<?php

/**
 * Loader
 *
 * Easy class autoloading.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

class Loader {
    protected static $paths = [];

    /**
     * Adds a path to class autoloader
     *
     * @param  string $path The path root to add to class autoloader
     * @param  string $namespace An optional name for path section
     * @return void
     */
    public static function addPath($path, $namespace=''){
        static::$paths[$path] = $namespace;
    }

    /**
     * Register core autoloader
     * @return bool Returns false if autoloader failed inclusion
     */
    public static function register(){
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        return spl_autoload_register(function($class){
            $cfile = strtr($class,'_\\','//') . '.php';
            foreach (static::$paths as $path => $namespace) {
              $namespace = strtr(trim($namespace,'\\'),'\\','/');
              if (strpos($cfile, $namespace) === 0) $cfile = substr($cfile, strlen($namespace));
              $file = rtrim($path,'/').'/'.$cfile;
              if (is_file($file)) return include_once($file);
            }
            return false;
        }, false, true);
    }

    public static function coreAsGlobal(){
      static $installed = false;
      ini_set('unserialize_callback_func', 'spl_autoload_call');
      return $installed ?: $installed = (bool)spl_autoload_register(function($class){
        if (strpos($class, "\\Core\\") !== 0){
          $translated_class = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
          if (!class_exists("\\Core\\$class", false) && file_exists($translated_class)) {
            include_once $translated_class;
            class_alias("\\Core\\$class", $class, false);
            return true;
          }
        }
        return false;
      }, false, true);
    }


}


