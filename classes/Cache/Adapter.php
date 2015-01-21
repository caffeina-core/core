<?php
	
/**
 * Cache\Adapter
 *
 * Cache drivers common interface.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace Cache;

interface Adapter  {
	public function get($key);
	public function set($key,$value,$expire=0);
	public function delete($key);
	public function exists($key);
	public function flush();

	public function inc($key,$value=1);
	public function dec($key,$value=1);

	public static function valid();
}