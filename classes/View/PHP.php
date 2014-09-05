<?php

/**
 * View\PHP
 *
 * Core\View PHP templates bridge.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace View;

class PHP {
    protected $templatePath = __DIR__;
    protected static $globals = [];
    const EXTENSION = 'php';

    public function __construct($path=null){
        if ($path) $this->templatePath = rtrim($path,'/') . '/';
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
        $sandbox = function() use ($template_path){
            ob_start();
            include($template_path);
            $__buffer__ = ob_get_contents();
            ob_end_clean();
            return $__buffer__;
        };
        $sandbox = $sandbox->bindTo(new PHPContext(
            array_merge(self::$globals,$data),
            $this->templatePath
        ));
        return $sandbox();
    }
}

class PHPContext {
    protected $data = [];
    protected $templatePath = __DIR__;
   
    public function __construct($data=[],$path=null){
        $this->data = $data;
        if ($path) $this->templatePath = rtrim($path,'/') . '/';
    }
    
    public function partial($template){
      $template_path = $this->templatePath . trim($template,'/') . '.php';
      include $template_path;        
    }
    
    public function __isset($n){return 1;}
    public function __unset($n){}
    public function __get($n){return (empty($this->data[$n])?'':$this->data[$n]);}
    
}
