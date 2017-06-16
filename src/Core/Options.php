<?php

/**
 * Options
 *
 * A dictionary to handle application-wide options.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace Core;

class Options extends Dictionary {
    use Filters;

    protected static $fields = null;

	/**
	 * Load a PHP configuration file (script must return array)
	 * @param  string $filepath The path of the PHP config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadPHP($filepath,$prefix_path=null){
		ob_start();
		$results = include($filepath);
		ob_end_clean();
		if($results) static::loadArray($results, $prefix_path, 'php');
	}

	/**
	 * Load an INI configuration file
	 * @param  string $filepath The path of the INI config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadINI($filepath,$prefix_path=null){
		$results = parse_ini_file($filepath,true);
		if($results) static::loadArray($results, $prefix_path, 'ini');
	}

	/**
	 * Load a JSON configuration file
	 * @param  string $filepath The path of the JSON config file
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadJSON($filepath,$prefix_path=null){
		$data = file_get_contents($filepath);
		$results = $data?json_decode($data,true):[];
		if($results) static::loadArray($results, $prefix_path, 'json');
	}

	/**
	 * Load an array to the configuration
	 * @param  array $array The array to load
	 * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
	 */
	public static function loadArray(array $array, $prefix_path=null, $type='array'){
    $array = static::filterWith(["load.$type", "load"], $array);
		if ($prefix_path){
			static::set($prefix_path, $array);
		} else {
			static::merge($array);
		}
    self::trigger('loaded');
	}

  /**
   * Load an ENV file
   * @param  string $dir The directory of the ENV file
   * @param  string $envname The filename for the ENV file (Default to `.env`)
   * @param  string $prefix_path You can insert/update the loaded array to a specific key path, if omitted it will be merged with the whole dictionary
   */
  public static function loadENV($dir,$envname='.env',$prefix_path=null){
    $dir = rtrim($dir,'/');
    $results = [];
    foreach(file("$dir/$envname", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
      $line = trim($line);
      if ($line[0] =='#' || strpos($line,'=')===false) continue;

      $parts = explode('=', $line, 2);

      $key   = trim(str_replace(['export ', "'", '"'], '', $parts[0]??''));
      $value = stripslashes(trim($parts[1]??'','"\''));

      $results[$key] = preg_replace_callback('/\${([a-zA-Z0-9_]+)}/',function($m) use (&$results){
        return isset($results[$m[1]]) ? $results[$m[1]] : '';
      }, $value);

      putenv("$key={$results[$key]}");
      $_ENV[$key] = $results[$key];
    }

    if ($results) static::loadArray($results, $prefix_path, 'env');
  }

}
