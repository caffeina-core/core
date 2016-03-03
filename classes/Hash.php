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

    public static function uuid($type=4, $namespace='', $name=''){
      switch($type){
        case 3: if(preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/Si', $namespace) !== 1) return false;
        $nhex = str_replace(array('-','{','}'), '', $namespace);
        $nstr = ''; for($i = 0; $i < strlen($nhex); $i+=2)
        $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        $hash = md5($nstr . $name);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
        substr($hash, 0, 8), substr($hash, 8, 4),
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
        substr($hash, 20, 12));
        case 5: if(preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/Si', $namespace) !== 1) return false;
        $nhex = str_replace(array('-','{','}'), '', $namespace);
        $nstr = ''; for($i = 0; $i < strlen($nhex); $i+=2)
        $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        $hash = sha1($nstr . $name);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
        substr($hash, 0, 8), substr($hash, 8, 4),
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
        substr($hash, 20, 12));
        default: case 4: return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
      }
    }


}
