<?php

/**
 * Cache\Redis
 *
 * Core\Cache Redis Driver.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */


namespace Cache;

class Redis implements CacheInterface {

  protected $redis = null;
  protected $options = [
    'scheme'      => 'tcp',
    'host'        => '127.0.0.1',
    'port'        => 6379,
    'timeout'     => 1,
    'reconnect'   => 100,
    'prefix'      => '',
    'serialize'   => true,
    'exceptions'  => true,
  ];

  public static function valid(){
    return true;
  }

  public function __construct($opt=[]){
    /**
     * Predis Docs:
     * https://github.com/nrk/predis
     */
    //require_once __DIR__.'/predis_0.8.7-dev.phar';
    $this->options = array_merge($opt,$this->options);
    try {
      $this->redis = new \Predis\Client($this->options['scheme'].'://'.$this->options['host'].':'.$this->options['port'].'/',[
        'prefix'              => 'core:'.$this->options['prefix'],
        'exceptions'          => $this->options['exceptions'],
        'connection_timeout'  => $this->options['timeout'],
      ]);
    } catch ( Exception $e ) {
      die($e);
    }
  }

  public function get($key){
    return $this->redis->get($key);
  }

  public function set($key,$value,$expire=0){
    $expire ? $this->redis->setex($key,$expire,$value) : $this->redis->set($key,$value);
  }

  public function delete($key){
  	$this->redis->delete($key);
  }

  public function exists($key){
    return $this->redis->exists($key);
  }

  public function flush(){
    $keys = $this->redis->keys('*');
    call_user_func_array([$this->redis,'del'],$keys);
  }

  public function inc($key,$value=1){
  	return $this->redis->incrby($key,$value);
  }

  public function dec($key,$value=1){
    return $this->redis->decrby($key,$value);
  }
}