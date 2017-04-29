<?php

/**
 * Cache\Files
 *
 * Core\Cache Files Driver.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace Core\Cache;

class Files implements Adapter {
    protected $options;

    public static function valid(){
        return is_writeable(sys_get_temp_dir());
    }

    public function __construct($options=[]){
        $this->options = (object) array_merge($options,[
            'cache_dir' => sys_get_temp_dir().'/core_file_cache',
        ]);
        $this->options->cache_dir = rtrim($this->options->cache_dir,'/');
        if(false===is_dir($this->options->cache_dir)) mkdir($this->options->cache_dir,0777,true);
        $this->options->cache_dir .= '/';
    }

    public function get($key){
        $cache_file_name = $this->options->cache_dir.$key.'.cache.php';
        if(is_file($cache_file_name) && $data = @unserialize(file_get_contents($cache_file_name))){
            if($data[0] && (time() > $data[0])) {
                unlink($cache_file_name);
                return null;
            }
            return $data[1];
        } else {
            return null;
        }
    }

    public function set($key,$value,$expire=0){
        $cache_file_name = $this->options->cache_dir.$key.'.cache.php';
        file_put_contents($cache_file_name,serialize([$expire?time()+$expire:0,$value]));
    }

    public function delete($key){
        $cache_file_name = $this->options->cache_dir.$key.'.cache.php';
      if(is_file($cache_file_name)) unlink($cache_file_name);
    }

    public function exists($key){
        $cache_file_name = $this->options->cache_dir.$key.'.cache.php';
        if(false === is_file($cache_file_name)) return false;
        $peek = file_get_contents($cache_file_name,false,null,-1,32);
        $expire = explode('{i:0;i:',$peek,2);
        $expire = explode(';',end($expire),2);
        $expire = current($expire);
        if($expire && $expire < time()){
            unlink($cache_file_name);
            return false;
        } else return true;
    }

    public function flush(){
        exec('rm -f ' . $this->options->cache_dir . '*.cache.php');
    }

    public function inc($key,$value=1){
        if(null === ($current = $this->get($key))) $current = $value; else $current += $value;
        $this->set($key,$current);
    }

    public function dec($key,$value=1){
        $this->inc($key,-abs($value));
    }
}
