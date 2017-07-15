<?php

/**
 * ZIP
 *
 * Archive compressed data.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

class ZIP {
  use Module;

  public $file,
         $name,
         $zip;

  /**
   * @return ZIP
   */
  public static function create($name='') : ZIP {
    return new ZIP($name);
  }

  public function __construct($name=''){
    $this->name = preg_replace('/\.zip$/','',($name?:tempnam(sys_get_temp_dir(), 'ZExp').'-archive'));
    $this->file = $this->name . '.zip';
    if (!preg_match('~^/|\./|\.\./~',$this->file)) $this->file = './'.$this->file;
    $this->zip  = new \ZipArchive;
    if ( true !== ($e = $this->zip->open($this->file,
      \ZipArchive::CREATE || \ZipArchive::OVERWRITE
    ))) {
      throw new \Exception("Error opening temp ZIP file [".($this->file)."] Code $e", 1);
    }
  }

  public function __destruct(){
    $this->close();
  }

  public function path(){
    return $this->file;
  }

  /**
   * @return ZIP
   */
  public function write($filename, $data) : ZIP {
    $this->zip->addFromString($filename, $data);
    return $this;
  }

  /**
   * @return ZIP
   */
  public function close() : ZIP {
    if($this->zip) @$this->zip->close();
    return $this;
  }

  /**
   * @return ZIP
   */
  public function addDirectory($folder, $root=null) : ZIP {
    $folder = rtrim($folder,'/');
    if (null === $root) {
      $root   = dirname($folder);
      $folder = basename($folder);
    }
    $this->zip->addEmptyDir($folder);
    foreach (glob("$root/$folder/*") as $item) {
      if (is_dir($item)) {
          $this->addDirectory(str_replace($root,'',$item),$root);
      } else if (is_file($item))  {
          $this->zip->addFile($item, str_replace($root,'',$item));
      }
    }

    return $this;
  }

  /**
   * @return void
   */
  public function download()  {
    @$this->zip->close();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment;filename="'.$this->name.'"',true);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($this->file));
    while(ob_get_level()) ob_end_clean();
    readfile($this->file);
    exit;
  }

}
