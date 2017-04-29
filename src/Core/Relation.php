<?php

/**
 * Relation trait
 *
 * Define a relation between two models.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core;

trait Relation {

  /**
   * [Internal] : Retrieve/Set relation options
   * This function can be used to get all options passing null, setting options passing an associative
   * array or retrieve a single value passing a string
   *
   * @param  mixed $options The options passed to the relation layer.
   * @return mixed          All options array or a single value
   */
  protected static function & relationOptions(){
    static $_options;
    if ($_options === null) $_options = (object)[ 'links' => [], 'relations' => [] ];
    return $_options;
  }

  /**
   * [Internal] : Assigns or retrieve the Save callback
   * The save callback interface is
   *   function($table, array $options)
   *
   * @param  callable $callback The callback to use on model save
   * @return callable           Current save callback
   */
  private static function relationAddRelationshipTo($link, $plurality, $extra=[]){
    $options = static::relationOptions();

    preg_match('((?<FOREIGN_CLASS>\w+)(\.(?<FOREIGN_KEY>\w+))?(:(?<LOCAL_KEY>\w+))?)', $link, $parts);

    $foreign_class = isset($parts['FOREIGN_CLASS']) ? $parts['FOREIGN_CLASS'] : false;
    $foreign_key   = isset($parts['FOREIGN_KEY'])   ? $parts['FOREIGN_KEY']   : false;
    $local_key     = isset($parts['LOCAL_KEY'])     ? $parts['LOCAL_KEY']     : false;

    if ( ! $foreign_class )
       throw new \Exception("[Core.Relation] Class ".get_called_class()." must define a foreign Model when assigning a relation.", 1);

    if ( ! is_subclass_of($foreign_class,'Core\\Model') )
       throw new \Exception("[Core.Relation] Class ".get_called_class()." want to relate to $foreign_class but it's not a Model.", 1);

    if ( ! $foreign_key ) {
      // Retrieve primary key from foreign class
      $foreign_key = $foreign_class::persistenceOptions("key");
    }

    if ( ! $local_key ) {
      // Retrieve local primary key
      $local_key = static::persistenceOptions("key");
    }

    $single = $plurality == 'single';

    $method = preg_replace_callback('([A-Z])', function($m){
      return "_" . strtolower($m[0]);
    }, lcfirst($foreign_class) . ($single ? '' : 's'));

    $hh = [$foreign_class,$foreign_key,$local_key];
    sort($hh);
    $options->links[md5(serialize($hh))] = $rel = (object)[
      'foreign_class' =>  $foreign_class,
      'foreign_key'   =>  $foreign_key,
      'local_key'     =>  $local_key,
      'single'        =>  $single,
      'method'        =>  $method,
      'extra'         =>  (object) $extra,
    ];

    if (empty($options->relations)) $options->relations = (object)[];
    $options->relations->$method = $getset = (object)[
      'get' => function($self) use ($foreign_class, $rel) {
         $val = $self->{$rel->local_key};
         $val = is_numeric($val) ? $val : "'" . addslashes($val) . "'";
         $data = $foreign_class::where("{$rel->foreign_key} = {$val}");
         return $rel->single ? current($data) : $data;
      },
      'set' => function($value, $self) use ($foreign_class, $rel) {
        if (!is_a($value, $foreign_class))
          throw new \Exception("[Core.Relation] Relationship for {$rel->method} must be of class $foreign_class.", 1);
        $self->local_key = $value->foreign_key;
        return $value;
      },
    ];

  }

  public function __get($name){
    $options = static::relationOptions();
    if (isset($options->relations->$name))
      return call_user_func($options->relations->$name->get, $this);
  }

  public function __set($name, $value){
    $options = static::relationOptions();
    if (isset($options->relations->$name))
      call_user_func($options->relations->$name->set, $value, $this);
  }

  public function __isset($name){
    $options = static::relationOptions();
    return isset($options->relations->$name);
  }

  public static function hasOne($modelName, $extra=[]){
    return static::relationAddRelationshipTo($modelName, 'single', $extra);
  }

  public static function hasMany($modelName, $extra=[]){
    return static::relationAddRelationshipTo($modelName, 'multiple', $extra);
  }

}
