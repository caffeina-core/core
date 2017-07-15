<?php

/**
 * FileSystemNative
 *
 * Native Filesystem
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace Core\FileSystem;

class Native implements Adapter {

  protected $root;

  public function __construct(array $options = []) {
      $this->root = empty($options['root'])?'/':(rtrim($options['root'],'/').'/');
  }

  /**
   * @return bool
   */
  public function exists($path){
      return file_exists($this->realPath($path));
  }

  /**
   * @return string|false
   */
  public function read($path){
      return $this->exists($path) ? file_get_contents($this->realPath($path)) : false;
  }

  /**
   * @return int
   */
  public function write($path, $data){
      $r_path = $this->realPath($path);
      if ( ! is_dir($r_dir = dirname($r_path)) ) @mkdir($r_dir,0775,true);
      return file_put_contents($r_path, $data);
  }

  /**
   * @return int
   */
  public function append($path, $data){
      return file_put_contents($this->realPath($path), $data, FILE_APPEND);
  }

  /**
   * @return bool
   */
  public function move($old, $new){
      // Atomic
      if($this->exists($old)){
          return $this->write($new,$this->read($old)) && $this->delete($old);
      } else return false;
  }

  /**
   * @return bool
   */
  public function delete($path){
      return $this->exists($path) ? unlink($this->realPath($path)) : false;
  }

  /**
   * @return       string[]
   * @psalm-return array<int, string>
   */
  public function search($pattern, $recursive=true){
      $results    = [];
      $root_len   = strlen($this->root);
      $rx_pattern = '('.strtr($pattern,['.'=>'\.','*'=>'.*','?'=>'.']).')Ai';

      /*
      $files = new \RegexIterator(new \RecursiveDirectoryIterator($this->root,
                   \RecursiveDirectoryIterator::SKIP_DOTS),$rx_pattern);
      foreach ($files as $path) {
          $results[] = trim(substr($path, $root_len),'/');
      }
      return $results;
      */

      $tree = new \RegexIterator(
              new \RecursiveIteratorIterator(
              new \RecursiveDirectoryIterator(
                  $this->root,
                  \RecursiveDirectoryIterator::SKIP_DOTS)),
                  $rx_pattern,\RegexIterator::GET_MATCH);

      $fileList = [];
      foreach($tree as $group) {
          foreach($group as $path) {
              $results[] = trim(substr($path, $root_len),'/');
          }
      }

      return $results;

  }

  /**
   * @return string
   */
  protected function realPath($path){
      return $this->root . $path;
  }

}
