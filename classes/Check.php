<?php

/**
 * Check
 *
 * Validate a data map against defined methods.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

class Check {
  use Module, Events;

  protected static $methods = [],
                   $errors  = [];
  public static    $data    = [];

  public static function valid($rules, $data){
    static::$errors = [];
    static::triggerOnce('init');
    self::$data = ($data = (array)$data);

    foreach ((array)$rules as $field_name => $rule) {

      $current = isset($data[$field_name]) ? $data[$field_name] : null;

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

        $meth_name = strtok($method, ':');
        $opts      = strtok(':') ?: '';
        $opts      = $opts ? json_decode("[$opts]") : [];
        $meth_opts = $opts ? array_merge([$current], $opts) : [$current];

        if ( static::$errors[$field_name] !== true ) continue 2;

        if (empty(static::$methods[$meth_name])) {
          static::$errors[$field_name] = true;
        } else {
          if (call_user_func_array(static::$methods[$meth_name]->validate,$meth_opts)){
            static::$errors[$field_name] = true;
          } else {
            $arg = [];
            foreach ($meth_opts as $key => $value) {
              $arg["arg_$key"] = $value;
            }
            static::$errors[$field_name] = Text::render(static::$methods[$meth_name]->message,$arg);
          }
        }
      }
    }

    self::$data = [];

    // Clean non-errors
    static::$errors = array_filter(static::$errors,function($v){
      return $v !== true;
    });

    return empty(static::$errors);
  }

   public static function method($name, $definition = null){
    if (is_array($name)) {
      foreach ($name as $method_name => $method_definition){
        if (is_callable($method_definition)) $method_definition = ['validate' => $method_definition];
        if (empty($method_definition['validate']) || !is_callable($method_definition['validate'])) continue;
        $method_definition['message']  = Filter::with("core.check.error.$method_name",@$method_definition['message']?:'Field not valid.');
        static::$methods[$method_name] = (object)$method_definition;
      }
    } else {
      if (is_callable($definition)) $definition = ['validate' => $definition];
      if (empty($definition['validate']) || !is_callable($definition['validate'])) return;
      $methods['message']     = Filter::with("core.check.error.$name",@$methods['message']?:'Field not valid.');
      static::$methods[$name] = (object)$definition;
    }
  }

  public static function errors() {
    return static::$errors;
  }

}

Check::on('init',function(){

  Check::method([

    'required' => [
      'validate' => function($value) {
          return (is_numeric($value) && $value==0) || !empty($value);
       },
       'message' => "This value cannot be empty.",
    ],

    'alphanumeric' => [
      'validate' => function($value) {
         return (bool)preg_match('/^[0-9a-zA-Z]+$/',$value);
      },
      'message' => "Value must be alphanumeric.",
    ],

    'numeric' => [
      'validate' => function($value) {
         return (bool)preg_match('/^\d+$/',$value);
      },
      'message' => "Value must be numeric.",
    ],

    'email' => [
      'validate' => function($value) {
         return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
      },
      'message' => "This is not a valid email.",
    ],

    'url' => [
      'validate' => function($value) {
         return (bool)filter_var($value, FILTER_VALIDATE_URL);
      },
      'message' => "This is not a valid URL.",
    ],

    'max' => [
      'validate' => function($value,$max) {
       return $value<=$max ? true : false;
    },
      'message' => "Value must be less than {{arg_1}}.",
    ],

    'min' => [
      'validate' => function($value,$min) {
         return $value >= $min;
      },
      'message' => "Value must be greater than {{arg_1}}.",
    ],

    'range' => [
      'validate' => function($value,$min,$max) {
         return ( $value >= $min ) && ( $value <= $max );
      },
      'message' => "This value must be in [{{arg_1}},{{arg_2}}] range.",
    ],

    'words' => [
      'validate' => function($value,$max) {
         return str_word_count($value) <= $max;
      },
      'message' => "Too many words, max count is {{arg_1}}.",
    ],

    'length' => [
      'validate' => function($value,$length) {
         return strlen($value) == $length;
      },
      'message' => "This value must be {{arg_1}} characters.",
    ],

    'min_length' => [
      'validate' => function($value,$min) {
         return strlen($value) >= $min;
      },
      'message' => "Too few characters, min count is {{arg_1}}.",
    ],

    'max_length' => [
      'validate' => function($value,$max) {
         return strlen($value) <= $max;
      },
      'message' => "Too many characters, max count is {{arg_1}}.",
    ],

    'true' => [
      'validate' => function($value) {
         return (bool)$value;
      },
      'message' => "This value must be true.",
    ],

    'false' => [
      'validate' => function($value) {
         return !$value;
      },
      'message' => "This value must be false.",
    ],

    'same_as' => [
      'validate' => function($value,$fieldname) {
       $x = isset(Check::$data[$fieldname]) ? Check::$data[$fieldname] : '';
         return $value == $x;
      },
      'message' => "Field must be equal to {{arg_1}}.",
    ],

    'in_array' => [
      'validate' => function($value,$array_values) {
         return  in_array($value, $array_values);
      },
      'message' => "This value is forbidden.",
    ],

  ]);

});

