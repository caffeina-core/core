<?php

/**
 * View\PHP
 *
 * Core\View PHP templates bridge.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace View;

class PHP implements Adapter {

    const EXTENSION 		  = 'php';

    protected $templatePath;
    protected static $globals = [];

    public function __construct($path=null,$options=[]){
        $this->templatePath = ($path ? rtrim($path,'/') : __DIR__) . '/';
    }
    
    public function exists($path){
        return is_file($this->templatePath.$path.'.php');
    }
    
    public static function addGlobal($key,$val){
      self::$globals[$key] = $val;
    }

    public static function addGlobals(array $defs){
      foreach ((array)$defs as $key=>$val) {
          self::$globals[$key] = $val;
      }
    }

    public function render($template,$data=[]){
        $template_path = $this->templatePath . trim($template,'/') . '.php';
        $sandbox 	   = function() use ($template_path){
            ob_start();
            include($template_path);
            $__buffer__ = ob_get_contents();
            ob_end_clean();
            return $__buffer__;
        };
        $sandbox = $sandbox->bindTo(new PHPContext(
            array_merge(self::$globals, $data),
            $this->templatePath
        ));
        return $sandbox();
    }
}

class PHPContext {
    protected $data 		 = [],
    		  $templatePath;
   
    public function __construct($data=[], $path=null){
        $this->data = $data;
        $this->templatePath = ($path ? rtrim($path,'/') : __DIR__) . '/';
    }
    
    public function partial($template, $vars=[]){
        return \View::from($template,array_merge($this->data,$vars));
    }
    
    public function __isset($n){ return true; }
    
    public function __unset($n){}

    public function __get($n){ 
    	return empty($this->data[$n]) ? '' : $this->data[$n];
    }
    
}
