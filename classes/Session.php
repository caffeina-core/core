<?php

/**
 * Session
 *
 * Manage PHP sessions.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Session {

	/**
	 * Start session handler
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function start($name=null){
		if (isset($_SESSION)) return;
		static::name($name);
		session_cache_limiter('must-revalidate');
		@session_start();
	}

	/**
	 * Get/Set Session name
	 *
	 * @access public
	 * @static
	 * @param string $key The session name
	 * @return string The session value
	 */
	static public function name($name=null){
		return $name ? session_name($name) : session_name();
	}

	/**
	 * Get a session variable reference
	 *
	 * @access public
	 * @static
	 * @param mixed $key The variable name
	 * @return mixed The variable value
	 */
	static public function & get($key,$default=null){
		static::start();
		if(isset($_SESSION[$key])==false) $_SESSION[$key] = is_callable($default)?call_user_func($default):$default;
		return $_SESSION[$key];
	}

	/**
	 * Set a session variable
	 *
	 * @access public
	 * @static
	 * @param mixed $key The variable name
	 * @param mixed $value The variable value
	 * @return void
	 */
	static public function set($key,$value=null){
		static::start();
		if($value==null && is_array($key)){
			foreach($key as $k=>$v) $_SESSION[$k]=$v;
		} else {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Delete a session variable
	 *
	 * @access public
	 * @static
	 * @param mixed $key The variable name
	 * @return void
	 */
	static public function delete($key){
		static::start();
		unset($_SESSION[$key]);
	}


	/**
	 * Delete all session variables
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function clear(){
		static::start();
		session_unset();
		session_destroy();
	}


	/**
	 * Check if a session variable exists
	 *
	 * @access public
	 * @static
	 * @param mixed $key The variable name
	 * @return bool
	 */
	static public function exists($key){
		static::start();
		return isset($_SESSION[$key]);
	}

	/**
	 * Return a read-only accessor to session variables for in-view use.
	 * @return SessionReadOnly
	 */
	static public function readOnly(){
		return new SessionReadOnly;
	}

}  /* End of class */



/**
 * Read-only Session accessor class
 */

class SessionReadOnly {

	/**
	 * Get a session variable reference
	 *
	 * @access public
	 * @param mixed $key The variable name
	 * @return mixed The variable value
	 */
	public function get($key){
		return Session::get($key);
	}
	public function __get($key){
		return Session::get($key);
	}

	public function name(){
		return Session::name();
	}

	/**
	 * Check if a session variable exists
	 *
	 * @access public
	 * @param mixed $key The variable name
	 * @return bool
	 */
	public function exists($key){
		return Session::exists($key);
	}
	public function __isset($key){
		return Session::exists($key);
	}

}  /* End of class */
