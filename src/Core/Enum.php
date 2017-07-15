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

namespace Core;

abstract class Enum {

  final private function __construct(){}

  final protected static function __constants(){
    static $_consts = null;
    return $_consts ?? $_consts = array_change_key_case(
        (new \ReflectionClass(get_called_class()))->getConstants()
      , CASE_UPPER);
  }

  final public static function key($value){
  	foreach (static::__constants() as $key => $const_val) {
  		if ($const_val === $value) return $key;
  	}
  	return false;
  }

  /**
   * @return bool
   */
  final public static function has($value){
    return isset(static::__constants()[strtoupper($value)]);
  }

}
