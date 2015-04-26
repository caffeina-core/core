<?php

/**
 * Hash
 *
 * Hashing shorthands.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */


class Hash {
    use Module;
   
    /**
     * Create ah hash for payload
     * @param  mixed $payload The payload string/object/array
     * @param  integer $method  The hashing method, default is "md5"
     * @return string          The hash string
     */
    public static function make($payload,$method='md5'){
        return hash($method,serialize($payload));
    }

    /**
     * Verify if given payload matches hash
     * @param  mixed $payload  The payload string/object/array
     * @param  string $hash    The hash string
     * @param  integer $method The hashing method
     * @return bool            Returns `true` if payload matches hash
     */
    public static function verify($payload,$hash,$method='md5'){
        return static::make($payload,$method) == $hash;
    }

    /**
     * List registered hashing algorithms
     *
     * @method methods
     *
     * @return array   Array containing the list of supported hashing algorithms.
     */
    public static function methods(){
        return hash_algos();
    }


    /**
     * Check if an alghoritm is registered in current PHP
     *
     * @method can
     *
     * @param  string $algo The hashing algorithm name
     *
     * @return bool
     */
    public static function can($algo){
        return in_array($algo,hash_algos());
    }
    
    /**
     * Static magic for creating hashes with a specified algorithm.
     * 
     * See [hash-algos](http://php.net/manual/it/function.hash-algos.php) for a list of algorithms
     */
    public static function __callStatic($method,$params){
        return self::make(current($params),$method);
    }

}
