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

    public function __construct($input=[],$deep=true){
        if (is_array($data = (is_string($input)?json_decode($input,1):(array)$input))){
            if ($deep) foreach ($data as $key => &$value) {
                if (is_array($value) || is_a($value,'stdClass')){
                    $value = new self($value);
                }
            }
           parent::__construct($data, static::ARRAY_AS_PROPS);
        } else {
            throw new InvalidArgumentException('Argument must be a string containing valid JSON, an array or an stdClass.');
        }
    }
 
    public function offsetSet($key, $value){   
        is_array($value) ? parent::offsetSet($key, new static($value)) : parent::offsetSet($key, $value);
    }
 
    public function offsetGet($key){
        $raw = parent::offsetGet($key);
        return is_callable($raw) ? call_user_func($raw) : $raw;
    }
 
    public function __call($method, $args){
        $raw = parent::offsetGet($method);
        if (is_callable($raw)) {
            if ($raw instanceof \Closure) $raw->bindTo($this);
            return call_user_func_array($raw, $args);
        }
    }
    
    public function __toString(){
        return json_encode($this,JSON_NUMERIC_CHECK);
    }
 
}
