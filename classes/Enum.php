<?php

/**
 * Enum
 *
 * A simple implementation of an Enum DataType.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2016 - http://caffeina.com
 */

abstract class Enum {

  final private function __construct(){}

  protected static function __constants(){
    static $_consts = null;
    if (null === $_consts) {
      $_consts = array_change_key_case(
        (new ReflectionClass(get_called_class()))->getConstants()
      , CASE_UPPER);
    }
    return $_consts;
  }

  public static function key($value){
  	foreach (static::__constants() as $key => $const_val) {
  		if ($const_val === $value) return $key;
  	}
  	return false;
  }

  public static function has($value){
    return isset(static::__constants()[strtoupper($value)]);
  }

}
