<?php

/**
 * CSV
 *
 * Comma Separated Values Tools.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

class CSV {
  use Module;

  const AUTO      = null,
        STANDARD  = ',',
        EXCEL     = ';',
        TAB       = "\t",
        READ      = 'r',
        WRITE     = 'w';

  protected $file,
            $headers      = [],
            $template     = [],
            $mode         = self::WRITE,
            $format       = self::STANDARD,
            $savedheaders = false;

  public static function open($file, $format=self::AUTO){
    return new static($file,self::READ,$format);
  }

  public static function create($file, $format=self::STANDARD){
    return new static($file,self::WRITE, $format);
  }

  public function SQL($sql){
    $csv = $this;
    SQL::each($sql,function($row) use (&$csv){
      $csv->write($row);
    });
    return $this;
  }
  
  public static function fromSQL($sql, $format=self::AUTO){
    return static::create(tempnam(sys_get_temp_dir(), 'CSVx'), $format)->SQL($sql);
  }

  public static function fromTable($table, $format=self::AUTO){
    $csv = static::create(tempnam(sys_get_temp_dir(), 'CSVx'), $format);
    foreach($table as $row){
      $csv->write($row);
    }
    return $csv;
  }

  public function __construct($file, $mode=self::READ, $format=self::AUTO){
    $this->mode = $mode;
    $this->file = new \SplFileObject($file,'c+');
    if (!$this->file->valid()) throw new Exception("Error opening CSV file [$file]", 1);
    $this->file->setFlags(
      \SplFileObject::READ_CSV |     // set file reading mode to csv
      \SplFileObject::SKIP_EMPTY |   // ignore empty lines
      \SplFileObject::DROP_NEW_LINE  // drop new line from last column in record
    );
    $this->format = ($format==self::AUTO ? $this->guessSeparator() : $format);
    $this->file->setCsvControl($this->format,'"',"\\");
  }

  private function guessSeparator($checkLines = 2){
    if ($this->mode == self::WRITE) return self::STANDARD;
    $delimiters = [",","\t",";"];
    $results = [];
    $this->file->rewind();
    while ($checkLines--) {
        $line = $this->file->fgets();
        foreach ($delimiters as $delimiter){
            $fields = preg_split('/['.$delimiter.']/', $line);
            if(count($fields) > 1){
                if(empty($results[$delimiter])){
                  $results[$delimiter] = 1;
                } else {
                  $results[$delimiter]++;
                }
            }
        }
    }
    $this->file->rewind();
    $results = array_keys($results, max($results));
    return $results[0];
  }

  public function write($row){
    if ($this->mode != self::WRITE) return;
    $row = (array)$row;
    if (false === $this->savedheaders) {
      $this->schema(array_keys($row));
    }
    $row_t = $this->template;
    foreach ($this->headers as $key) {
      if (isset($row[$key])) $row_t[$key] = $row[$key];
    }
    $this->file->fputcsv($row_t);
  }

  public function read(){
    if ($this->mode != self::READ) return;
    foreach($this->file as $row){
      if ($row){
        if(!$this->headers) {
          $this->headers = $row;
          continue;
        }
        yield array_combine($this->headers, array_map('trim', $row));
      }
    }
    return;
  }

  public function each(callable $looper = null){
    if ($looper) {
      foreach($this->read() as $k => $row) $looper($row, (int)$k);
      return $this;
    } else {
      $results = [];
      foreach($this->read() as $row) $results[] = $row;
      return $results;
    }
  }

  public function convert($filename, $format=self::STANDARD){
    if ($this->mode != self::READ) return;
    if ($format == self::AUTO) $format = self::STANDARD;
    $csv = CSV::create($filename, CSV::EXCEL);
    $this->each(function($row) use ($csv) {
      $csv->write($row);
    });
    return $csv;
  }

  public function flush(){
    if ($this->mode == self::WRITE) {
      $this->file->fflush();
    }
  }

  public function schema($schema=null){
    if($schema){
      $this->headers = array_values((array)$schema);
      if ($this->mode == self::WRITE) {
        $this->savedheaders = true;
        $this->template = array_combine($this->headers, array_pad([],count($this->headers),''));
        $this->file->fputcsv($this->headers);
      }
      return $this;
    } else {
      return $this->headers;
    }
  }

  public function asString(){
    $this->flush();
    return file_get_contents($this->file->getPathname());
  }

  public function __toString(){
    try { return $this->asString(); } catch(\Exception $e) { return ''; }
  }

}
