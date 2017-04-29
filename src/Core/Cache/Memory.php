<?php

/**
 * Cache\Memory
 *
 * Core\Cache Memory Driver.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace Core\Cache;

class Memory implements Adapter {

  protected $memory = [];

    public static function valid(){
        return true;
    }

    public function get($key){
      if(isset($this->memory[$key])){
        if($this->memory[$key][1] && (time() > $this->memory[$key][1])) {
          unset($this->memory[$key]);
          return null;
        }
        return $this->memory[$key][0];
      }
    }

    public function set($key,$value,$expire=0){
      $this->memory[$key] = [$value,$expire?time()+$expire:0];
    }

    public function delete($key){
      unset($this->memory[$key]);
    }

    public function exists($key){
      return isset($this->memory[$key]) && (!$this->memory[$key][1] || (time() <= $this->memory[$key][1]));
    }

    public function flush(){
      $this->memory = [];
    }

    public function inc($key,$value=1){
      return isset($this->memory[$key]) ? $this->memory[$key][0] += $value : $this->memory[$key][0] = $value;
    }

    public function dec($key,$value=1){
        $this->inc($key,-abs($value));
    }
}
