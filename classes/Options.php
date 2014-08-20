<?php

/**
 * Options
 *
 * A dictionary to handle application-wide options.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Options extends Dictionary {
  protected static $fields = [];

	/**
	 * Load a PHP configuration file (script must return array())
	 * @param  string $filepath The path of the PHP config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadPHP($filepath,$prefix_path=null){
		ob_start();
		$results = include($filepath);
		ob_end_clean();
		static::load($results,$prefix_path);
	}

	/**
	 * Load an INI configuration file
	 * @param  string $filepath The path of the INI config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadINI($filepath,$prefix_path=null){
		static::load(parse_ini_file($filepath,true),$prefix_path);
	}

	/**
	 * Load a JSON configuration file
	 * @param  string $filepath The path of the JSON config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadJSON($filepath,$prefix_path=null){
		static::load(json_decode($filepath,true),$prefix_path);
	}

	/**
	 * Load an array to the configuration
	 * @param  string $array The array to load
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function load($array,$prefix_path=null){
		if (is_array($array)) {
			if ($prefix_path){
				static::set($prefix_path,$array);
			} else {
				static::merge($array);
			}
		}
	}

}
