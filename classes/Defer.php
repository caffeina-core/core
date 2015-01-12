<?php

/**
 * Defer
 *
 * Execute code after script resolution.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Defer {
	
	use Module;
	
	protected static $inited = false;
	 
	/**
	 * Defer callback execution after script execution
	 * @param callable $callback The deferred callback
	 */	
	public static function after(callable $callback){
		static::$inited || static::install();
		Event::on('core.shutdown',$callback);
	}
	
	/**
	 * Single shot defer handeler install
	 */
	protected static function install(){
		if (static::$inited) return;
		
		// Disable time limit
		set_time_limit(0);
		
		// HHVM support
		if(function_exists('register_postsend_function')){
			register_postsend_function(function(){
				Event::trigger('core.shutdown');
			});
		} else if(function_exists('fastcgi_finish_request')) {
			register_shutdown_function(function(){
				fastcgi_finish_request();
				Event::trigger('core.shutdown');
			});				
		} else {
			register_shutdown_function(function(){
				Event::trigger('core.shutdown');
			});
		}

		static::$inited = true;
	}	
}
