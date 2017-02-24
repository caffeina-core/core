<?php

/**
 * Password
 *
 * Password hashing.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

 class Password {
    use Module;

    /**
     * Create a secure password hash.
     * @param string $password
     * @return string
     */
    public static function make($password){
        // Pre PHP 5.5 support
        if (!defined('PASSWORD_DEFAULT')) {
            return '$5h$'.hash('sha1',$password);
        } else {
            return password_hash($password,PASSWORD_BCRYPT,['cost' => 12]);
        }
    }

    /**
     * Verify if password match a given hash
     * @param  string $password The password to check
     * @param  string $hash     The hash to match against
     * @return bool             Returns `true` if hash match password
     */
    public static function verify($password, $hash){
        // Pre PHP 5.5 support
        if (!defined('PASSWORD_DEFAULT') || substr($hash,0,4)=='$5h$') {
            return '$5h$'.hash('sha1',$password) == $hash;
        } else {
            return password_verify($password,$hash);
        }
    }

    /**
     * Helper for secure time-constant string comparison
     * Protect from time-based brute force attacks.
     * @param  string $a First string to compare
     * @param  string $b Second string to compare
     * @return bool      Returns `true` if strings are the same
     */
    public static function compare($a, $b){
      return hash_equals($a, $b);
    }

}


// Polyfill hash_equals (PHP < 5.6.0)
// http://php.net/manual/en/function.hash-equals.php
if(!function_exists('hash_equals')) {
  function hash_equals($a, $b) {
    return substr_count("$a" ^ "$b", "\0") * 2 === strlen("$a$b");
  }
}
