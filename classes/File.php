<?php
/**
 * File
 *
 * Filesystem utilities.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class File {
	use Module;

    /**
     * Helper for reading a local/remote file
     * @param  string $path The file path
     * @return string       The file content
     */
    public static function read($path){
        return file_get_contents($path);        
    }
    
    /**
     * Helper for writing a local/remote file
     * @param  string $path The file path
     * @param  string $data The file content
     */
    public static function write($path,$data){
        file_put_contents($path,$data);      
    }

    /**
     * Helper for appending to a local/remote file
     * @param  string $path The file path
     * @param  string $data The file content
     */
    public static function append($path,$data){
        file_put_contents($path,$data,FILE_APPEND|LOCK_EX);      
    }


    /**
     * Run a recursive glob from a starting folder filtering by a pattern. 
     * @param  string $folder  The starting folder to search
     * @param  [type] $pattern The glob syntax pattern (* wildcard, ? single char)
     * @return [type]          [description]
     */
     public static function search($folder,$pattern,$recursive=true){
       exec('ls -1 '.rtrim($folder,'/').($recursive?'/**/':'/').$pattern,$res);
       return $res?:[];
     }
    
}
