<?php

/**
 * Object
 *
 * Access properties with associative array or object notation seamlessly.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Object extends ArrayObject {

    /**
     * An Object can wrap a StdClass, an array or an object from a JSON encoded string.
     * 
     * This class is useful for wrapping API responses and access their properties in
     * an easy way.
     * 
     * @param mixed  $input The object/array/json_encoded object to wrap
     * @param boolean $deep  Wrap also deep branches as Objects
     */
    public function __construct($input=[], $deep=true){
        $data = is_string($input) ? json_decode($input,true) : (array)$input;
        if (is_array($data)){
            if ($deep) {
                foreach ($data as $key => &$value) {
                    if (is_array($value) || is_a($value,'stdClass')){
                        $value = new self($value);
                    }
                }
            }
           parent::__construct($data, static::ARRAY_AS_PROPS);
        } else {
            throw new InvalidArgumentException(
                'Argument must be a string containing valid JSON, an array or an stdClass.'
            );
        }
    }
 
    /**
     * ArrayObject::offsetSet
     */
    public function offsetSet($key, $value){   
        if ( is_array($value) )
          parent::offsetSet($key, new static($value));
        else
          parent::offsetSet($key, $value);
    }
 
    /**
     * ArrayObject::offsetGet
     */
    public function offsetGet($key){
        $raw = parent::offsetGet($key);
        return is_callable($raw) ? call_user_func($raw) : $raw;
    }
 
    /**
     * Emulate object methods
     */
    public function __call($method, $args){
        $raw = parent::offsetGet($method);
        if (is_callable($raw)) {
            if ($raw instanceof \Closure) $raw->bindTo($this);
            return call_user_func_array($raw, $args);
        }
    }
    
    /**
     * If casted as a string, return a JSON rappresentation of the wrapped payload
     * @return string
     */
    public function __toString(){
        return json_encode($this,JSON_NUMERIC_CHECK);
    }
    
    /**
     * Dot-Notation Array Path Resolver
     * @param  string $path The dot-notation path
     * @param  array $root The array to navigate
     * @return mixed The pointed value 
     */
    public static function fetch($path, & $root) {
      $frag = strtok($path,'.');
      $ptr = $root;
      
      if (is_object($root)) {
        while (
          ( $ptr = isset($ptr[$frag]) ? $ptr[$frag] : '' )
          &&
          ( $frag = strtok('.') )
        );
      } else if (is_array($root)) {
        while (
          ( $ptr = isset($ptr->$frag) ? $ptr->$frag : '' )
          &&
          ( $frag = strtok('.') )
        );
      }
    
      return $frag ? '' : $ptr;
    }

    public static function create($class, $args = null){
        return is_array($args) ? (new ReflectionClass($class))->newInstanceArgs($args) : new $class;
    }
    
    public static function canBeString($var) {
      return $var === null || is_scalar($var) || is_callable([$var, '__toString']);
    }
 
}
