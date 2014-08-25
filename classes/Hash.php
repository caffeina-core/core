<?php

/**
 * Hash
 *
 * Hashing shorthands.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */


class Hash {
    const FAST = 0;
    const SLOW = 1;
    
    /**
     * Create ah hash for payload
     * @param  mixed $payload The payload string/object/array
     * @param  integer $method  The hashing method : FAST/SLOW
     * @return string          The hash string
     */
    public static function make($payload,$method=self::FAST){
        $_payload = serialize($payload);
        switch($method){
            case self::SLOW:
                return hash('sha1',$_payload);
            case self::FAST:
            default:
                return hash('md5',$_payload);
        }
    }

    /**
     * Verify if given payload matches hash
     * @param  mixed $payload  The payload string/object/array
     * @param  string $hash    The hash string
     * @param  integer $method The hashing method : FAST/SLOW
     * @return bool            Returns `true` if payload matches hash
     */
    public static function verify($payload,$hash,$method=self::FAST){
        return static::make($payload,$method) == $hash;
    }
    
}

