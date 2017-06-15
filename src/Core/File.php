<?php

/**
 * File
 *
 * Filesystem utilities.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace Core;

abstract class File {
  use Module,
      Events;

  protected static $mount_points = [];

  final public static function mount($alias, $driver, $options = null) {
    $driver_class = '\\Core\\FileSystem\\'.ucfirst(strtolower($driver));
    if (!class_exists($driver_class)) throw new \Exception('Filesystem adapter '.$driver.' not found.');
    static::$mount_points[$alias] = new $driver_class($options);
    static::trigger("mount",$alias, $driver_class, static::$mount_points[$alias]);
  }

  final public static function unmount($alias) {
    unset(static::$mount_points[$alias]);
    static::trigger("unmount",$alias);
  }

  final public static function mounts() {
    return array_keys(static::$mount_points);
  }

  final public static function __callStatic($name, $params) {
    $uri = array_shift($params);
    if ($file_location = static::locate($uri)){
      list($mount, $path) = $file_location;
      array_unshift($params, static::resolvePath($path));
      if (empty(static::$mount_points[$mount])) return false;
      return call_user_func_array([static::$mount_points[$mount],$name],$params);
    } else return false;
  }

  final public static function locate($path) {
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

  final public static function resolvePath($path) {
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

  final public static function search($pattern, $recursive=true){
    $results = [];
    foreach (static::$mount_points as $mount => $fs) {
      foreach($fs->search($pattern, $recursive) as $path) {
        $results[] = $mount.'://'.$path;
      }
    }
    return $results;
  }


  final public static function move($old,$new) {
    $src  = static::locate($old);
    $dest = static::locate($new);
    if ($src && $dest) {
      $_sfs = static::$mount_points[$src[0]];
      $_dfs = static::$mount_points[$dest[0]];
      if ($src[0] == $dest[0]) {
        // Same filesystem
        return $_sfs->move($src[1],$dest[1]);
      } else {
        return $_dfs->write($dest[1],$_sfs->read($src[1])) && $_sfs->delete($src[1]);
      }
    } else return false;
  }

}

