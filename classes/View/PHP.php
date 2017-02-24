<?php

/**
 * View\PHP
 *
 * Core\View PHP templates bridge.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace View;

class PHP implements Adapter {

  const EXTENSION = '.php';

  protected static $templatePath,
                   $options = [],
                   $globals = [];

  public function __construct($path=null, $options=[]){
      self::$options = $options;
      self::$templatePath = ($path ? rtrim($path,'/') : __DIR__) . '/';
  }

  public static function exists($path){
      return is_file(self::$templatePath . $path . static::EXTENSION);
  }

  public static function addGlobal($key,$val){
    self::$globals[$key] = $val;
  }

  public static function addGlobals(array $defs){
    foreach ($defs as $key=>$val) {
        self::$globals[$key] = $val;
    }
  }

  public function render($template, $data=[]){
      $template_path = self::$templatePath . trim($template,'/') . static::EXTENSION;
      $sandbox = function() use ($template_path){
          ob_start();
          include($template_path);
          $__buffer__ = ob_get_contents();
          ob_end_clean();
          return $__buffer__;
      };
      return call_user_func($sandbox->bindTo(new PHPContext(
          array_merge(self::$globals, $data),
          self::$templatePath
      )));
  }
}

class PHPContext {
  protected $data = [],
            $path;

  public function __construct($data=[], $path=null){
      $this->data = $data;
      $this->path = $path;
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
