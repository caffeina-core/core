<?php

/**
 * File\Native
 *
 * Native Filesystem
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace File;

class Native implements \FileInterface {
    protected $root;
    
    public function __construct(array $options = []) {
        $this->root = empty($options['root'])?'/':(rtrim($options['root'],'/').'/');
    }
    
    public function exists($path){
        return file_exists($this->realPath($path));
    }
    
    public function read($path){
        return $this->exists($path) ? file_get_contents($this->realPath($path)) : false;
    }
    
    public function write($path, $data){
        return file_put_contents($this->realPath($path), $data);
    }
    
    public function append($path, $data){
        return file_put_contents($this->realPath($path), $data, FILE_APPEND);        
    }

    public function delete($path){
        return $this->exists($path) ? unlink($this->realPath($path)) : false;
    }
    
    public function search($pattern, $recursive=true){
        $results = [];
        $root_len = strlen($this->root);
        $rx_pattern = '('.strtr($pattern,['.'=>'\.','*'=>'.*','?'=>'.']).')Ai';
        $files = new \RegexIterator(new \RecursiveDirectoryIterator($this->root,
                     \RecursiveDirectoryIterator::SKIP_DOTS),$rx_pattern);
        foreach ($files as $path) {
            $results[] = trim(substr($path, $root_len),'/');
        }
        return $results;
    }
    
    protected function realPath($path){
        return $this->root . $path;
    }

}
