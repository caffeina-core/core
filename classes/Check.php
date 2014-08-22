<?php

/**
 * Check
 *
 * Validate a data map against defined methods.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Check {
  protected static $methods = [];
  protected static $errors = [];

  public static function valid($rules,$data){
    static::$errors = [];

    Event::triggerOnce('core.check.init');

    foreach ($rules as $field_name => $rule) {

      $current = isset($data[$field_name])?$data[$field_name]:null;

      if (is_callable($rule)){
        static::$errors[$field_name] = call_user_func($rule,$current);
        continue;
      } elseif (is_string($rule)) {
        $current_rules = array_flip(preg_split('/\s*\|\s*/', $rule));
      } else {
        $current_rules = (array)$rule;
      }

      static::$errors[$field_name] = true;

      foreach($current_rules as $method => $message) {

        $meth_name = strtok($method,':');
        $meth_opts = array_merge([$current],json_decode('['.strtok(':').']'));

        if ( static::$errors[$field_name] !== true ) continue 2;
        static::$errors[$field_name] =
            isset(static::$methods[$meth_name]) ?
                call_user_func_array(static::$methods[$meth_name],$meth_opts) : true;
      }
    }

    // Clean non-errors
    static::$errors = array_filter(static::$errors,function($v){
      return $v!==true;
    });

    return empty(static::$errors);
  }

  public static function method($name,callable $callback = null){
    if (is_array($name)){
      foreach ($name as $method_name => $method_callback){
          static::$methods[$method_name] = $method_callback;
      }
    } else {
      static::$methods[$name] = $callback;
    }
  }

  public static function errors(){
    return static::$errors;
  }

}

Event::on('core.check.init',function(){

  Check::method([

    'required' => function($value){
      return empty($value)?'Questo dato è necessario.':true;
    },

    'alphanumeric' => function($value){
       return preg_match('/^\w+$/',$value)?true:'I valore deve essere alfanumerico.';
    },

    'numeric' => function($value){
       return preg_match('/^\d+$/',$value)?true:'I valore deve essere numerico.';
    },

    'email' => function($value){
       return filter_var($value,FILTER_VALIDATE_EMAIL)?true:'È necessaria una email valida.';
    },

    'url' => function($value){
       return filter_var($value,FILTER_VALIDATE_URL)?true:'È necessaria una URL valida.';
    },

    'max' => function($value,$max){
       return $value<=$max?true:'Il valore può essere al massimo '.$max.'.';
    },

    'min' => function($value,$min){
       return $value>=$min?true:'Il valore può essere come minimo '.$min.'.';
    },

    'words' => function($value,$max){
       return str_word_count($value)<=$max?true:'Ci possono essere al massimo '.$max.' parole.';
    },

    'length' => function($value,$max){
       return strlen($value)<=$max?true:'Ci possono essere al massimo '.$max.' caratteri.';
    },

  ]);

});

