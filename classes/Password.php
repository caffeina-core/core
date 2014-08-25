<?php

/**
 * Password
 *
 * Password hashing.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

 class Password {

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
     * @return bool           Returns `true` if hash match password 
     */
    public static function verify($password,$hash){
    	// Pre PHP 5.5 support
        if (!defined('PASSWORD_DEFAULT') || substr($hash,0,4)=='$5h$') {
            return '$5h$'.hash('sha1',$password) == $hash;
        } else {
            return password_verify($password,$hash);
        }
    }
}

