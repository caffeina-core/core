<?php

/**
 * ZIP
 *
 * Archive compressed data.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class ZIP {
  use Module;

  protected $file,
            $name,
            $zip;

  public static function create($name){
    return new self($name);
  }

  public function __construct($name){
    $this->name = $name;
    $this->file = tempnam(sys_get_temp_dir(), 'ZExp');
    $this->zip = new ZipArchive();
    if (!$this->zip->open($this->file, ZIPARCHIVE::CREATE))
      throw new Exception("Error opening temp ZIP file [".($this->file)."]", 1);
  }

  public function __destruct(){
    $this->close();
  }

  public function write($filename, $data){
    $this->zip->addFromString($filename, $data);
  }

  public function close(){
    if($this->zip) $this->zip->close();
  }

  public function download(){
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

