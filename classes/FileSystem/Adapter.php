<?php

/**
 * FileSystem\Adapter Interface
 *
 * A Virtual Filesystem
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace FileSystem;

interface Adapter {
	
	public function exists($path);
	public function read($path);
	public function write($path, $data);
	public function append($path, $data);
	public function delete($path);
	public function move($old_path, $new_path);
	public function search($pattern, $recursive=true);
		
}
