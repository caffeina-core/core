<?php

/**
 * FileSystem\ZIP
 *
 * ZIP Archive Filesystem
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace FileSystem;

class ZIP implements Adapter {

    protected $path,
              $zipfile,
              $fileCache;

    public function __construct(array $options = []) {
        $this->path    = empty($options['root'])?(tempnam(sys_get_temp_dir(), 'CFZ_').'.zip'):rtrim($options['root']);
        $this->zipfile = new \ZipArchive();
        if ( !$this->zipfile->open($this->path, \ZipArchive::CREATE) ){
            throw new \Exception("File::ZIP Cannot open or create ".$this->path);
        }
    }

    public function exists($path){
        return false !== $this->zipfile->locateName($path);
    }

    public function read($path){
        if (isset($this->fileCache[$path])) return $this->fileCache[$path];
        return $this->exists($path) ? $this->zipfile->getFromName($path) : false;
    }

    public function write($path, $data){
        // This is needed because we cant write and read from the same archive.
        $this->fileCache[$path] = $data;
        return $this->zipfile->addFromString($path, $data);
    }

    public function append($path, $data){
        return $this->write($path, ($this->read($path) ?: '') . $data);
    }

    public function delete($path){
        return $this->exists($path) ? $this->zipfile->deleteName($path) : false;
    }

    public function move($old, $new){
        // Atomic rename
        // This is needed because we cant write and read from the same archive.
        return $this->write($new,$this->read($old)) && $this->delete($old);
        // return $this->zipfile->renameName($old, $new);
    }

    public function search($pattern, $recursive=true){
        $results = [];
        $rx_pattern = '('.strtr($pattern,['.'=>'\.','*'=>'.*','?'=>'.']).')Ai';

        for( $i = 0, $c = $this->zipfile->numFiles; $i < $c; $i++ ){
            $stat = $this->zipfile->statIndex( $i );
            if (preg_match($rx_pattern,$stat['name'])) $results[] = $stat['name'];
        }

        return $results;
    }

}
