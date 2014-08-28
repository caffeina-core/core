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
      return (is_numeric($value) && $value==0) || empty($value)?'This value cant\' be empty.':true;
    },

    'alphanumeric' => function($value){
       return preg_match('/^\w+$/',$value)?true:'Value must be alphanumeric.';
    },

    'numeric' => function($value){
       return preg_match('/^\d+$/',$value)?true:'Value must be numeric.';
    },

    'email' => function($value){
       return filter_var($value,FILTER_VALIDATE_EMAIL)?true:'This is not a valid email.';
    },

    'url' => function($value){
       return filter_var($value,FILTER_VALIDATE_URL)?true:'This is not a valid URL.';
    },

    'max' => function($value,$max){
       return $value<=$max?true:'Value must be less than '.$max.'.';
    },

    'min' => function($value,$min){
       return $value>=$min?true:'Value must be greater than '.$min.'.';
    },
    
    'range' => function($value,$min,$max){
       return (($value>=$min)&&($value<=$max)) ? true : "This value must be in [$min,$max] range.";
    },
    
    'words' => function($value,$max){
       return str_word_count($value)<=$max?true:'Too many words, max count is '.$max.'.';
    },

    'length' => function($value,$max){
       return strlen($value)<=$max?true:'Too many characters, max count is '.$max.'.';
    },

  ]);

});

