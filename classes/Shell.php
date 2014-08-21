<?php

/**
 * Shell
 *
 * A shell access proxy.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Shell {
    protected static $aliases = [];
    protected $command;
    
    /**
     * Compile a shell command
     * @param string $command 
     * @param array $params 
     * @return string
     */
    protected static function _compileCommand($command,array $params){
        $s = $w = [];
        foreach ($params as $p) {
            if ($p instanceof static) {
              $s[] = '$('.$p->getShellCommand().')';
            } else if (is_array($p)) foreach ($p as $key => $value) {
                if(is_numeric($key)){
                  $w[] = '--'.$value;                      
                } else {
                  if(is_bool($value)){
                     if($value) $w[] = '--'.$key;
                  } else { 
                    $w[] = '--'.$key.'='.escapeshellarg($value);  
                  }                    
                }
            } else {
              $s[] = $p;
            }
        }
        return trim(
            '/usr/bin/env '.$command.' '.implode(' ',array_merge($w,$s))
        );
    }
 
    /**
     * Returns the compiled shell command
     * @return string
     */
    public function getShellCommand(){
        return $this->command;
    }

    public static function __callStatic($command,$params){
        // Check if is an alias
        if (isset(static::$aliases[$command])){
            if(!$results = call_user_func_array(static::$aliases[$command],$params))
                throw new Exception('Shell aliases must return a Shell class or a command string.');
            return $results instanceof static? $results : new static($results);
        } else {
            return new static($command,$params);
        }
    }
    
    public function __construct($command,$params=null){
        $this->command = $params?static::_compileCommand($command,$params):$command;
    }
    
    public function __toString(){
        $output = [];
        exec($this->command,$output,$error_code);
        return empty($output)?'':implode(PHP_EOL,$output);
    }

    /**
     * Concatenate multiple shell commands via piping
     * @return Shell The piped shell command
     */
    public static function pipe(/* ... */){
        $cmd = [];
        foreach (func_get_args() as $item) {
            $cmd[] = ($item instanceof static)?$item->getShellCommand():$item;
        }
        return new static(implode(' | ',$cmd));
    }

    /**
     * Concatenate multiple shell commands via logical implication ( && )
     * @return Shell The concatenated shell command
     */
    public static function sequence(/* ... */){
        $cmd = [];
        foreach (func_get_args() as $item) {
            $cmd[] = ($item instanceof static)?$item->getShellCommand():$item;
        }
        return new static(implode(' && ',$cmd));
    }

    public static function execCommand($command,$params){
        return new static($command,$params);
    }

    public static function alias($command,callable $callback){
        static::$aliases[$command] = $callback;
    }

    public static function escape($arg){
        return escapeshellarg($arg);
    }

    public function run(){
        return $this->__toString();
    }

} /* End of class */

