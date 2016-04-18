<?php

/**
 * FileSystem\Memory
 *
 * Temp Memory Filesystem
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace FileSystem;

class Memory implements Adapter {

  protected $storage = [];

  public function exists($path){
    return isset($this->storage[$path]);
  }

  public function read($path){
    return $this->exists($path) ? $this->storage[$path] : false;
  }

  public function write($path, $data){
    $this->storage[$path] = $data;
  }

  public function append($path, $data){
    @$this->storage[$path] .= $data;
  }

  public function move($old, $new){
    if($this->exists($old)){
      $this->storage[$new] = $this->storage[$old];
      unset($this->storage[$old]);
      return true;
    } else return false;
  }

  public function delete($path){
    unset($this->storage[$path]);
    return true;
  }

  public function search($pattern, $recursive=true){
    $results = [];
    $rx_pattern = '('.strtr($pattern,['.'=>'\.','*'=>'.*','?'=>'.']).')Ai';
    foreach (array_keys($this->storage) as $path) {
      if (preg_match($rx_pattern,$path)) $results[] = $path;
    }
    return $results;
  }
}
