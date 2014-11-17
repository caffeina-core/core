<?php

/**
 * CSV
 *
 * Comma Separated Values Tools.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 0.7.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class CSV {
  use Module;

  const STANDARD = ',';
  const EXCEL = ';';

  protected $file,
            $handle,
            $format = self::STANDARD,
            $savedheaders=false;

  public static function open($file,$format=self::STANDARD){
    return new self($file,$format);
  }

  public static function fromSQL($sql,$format=self::STANDARD){
    $csv = new self(tempnam(sys_get_temp_dir(), 'CSVx'),$format);
    SQL::all($sql,function($row) use (&$csv){
      $csv->write($row);
    });
    return $csv->asString();
  }

  public static function fromTable($table,$format=self::STANDARD){
    $csv = new self(tempnam(sys_get_temp_dir(), 'CSVx'),$format);
    foreach($table as $row){
      $csv->write($row);
    }
    return $csv->asString();
  }

  public function __construct($file,$format=self::STANDARD){
    if (!$this->handle = fopen($this->file=$file,"w")) throw new Exception("Error opening CSV file [$file]", 1);
    $this->format = $format;
  }

  public function __destruct(){
    $this->close();
  }

  public function write($row){
    if (false === $this->savedheaders) {
      $this->savedheaders = true;
      fputcsv($this->handle,array_keys((array)$row),$this->format);
    }
    fputcsv($this->handle,array_values((array)$row),$this->format);
  }

  public function close(){
    if($this->handle) @fclose($this->handle);
  }

  public function asString(){
    $this->close();
    return file_get_contents($this->file);
  }

}