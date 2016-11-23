<?php

/**
 * Persistence trait
 *
 * Provides a way to persist a class on a storage.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

trait Persistence {

  /**
   * [Internal] : Retrieve/Set persistence options
   * This function can be used to get all options passing null, setting options passing an associative
   * array or retrieve a single value passing a string
   *
   * @param  mixed $options The options passed to the persistence layer.
   * @return mixed          All options array or a single value
   */
  public static function persistenceOptions($options=null){
    static $_options = ['table'=>null,'key'=>'id'];
    if ($options === null) return $_options;

    if (is_array($options)) {
      foreach ($_options as $key => &$value) {
        if (isset($options[$key])) $value = $options[$key];
      }
      return $_options;
    } else {
      if (empty($_options['table'])) {
        $self = get_called_class();
        if (defined("$self::_PRIMARY_KEY_")){
          $x = explode('.', $self::_PRIMARY_KEY_);
          $_options = [
            'table' => current($x),
            'key'   => isset($x[1])?$x[1]:'id',
          ];
        } else {
          // User pluralized class name as default table
          switch(substr($s = strtolower($self),-1)){
              case 'y': $table = substr($s,0,-1).'ies'; break;
              case 's': $table = substr($s,0,-1).'es';  break;
              default:  $table = $s.'s'; break;
          }
          // Default ID
          $_options = [
            'table' => $table,
            'key'   => 'id',
          ];
        }
      }
      return isset($_options[$options]) ? $_options[$options] : '';
    }
  }

  /**
   * [Internal] : Assigns or retrieve the Save callback
   * The save callback interface is
   *   function($table, array $options)
   *
   * @param  callable $callback The callback to use on model save
   * @return callable           Current save callback
   */
  protected static function persistenceSave(callable $callback=null){
    static $save_cb = null;
    return $callback ? $save_cb = $callback : $save_cb;
  }

  /**
   * [Internal] : Assigns or load the Load callback
   * The load callback interface is
   *   function($table, array $options)
   *
   * @param  callable $callback The callback to use on model load
   * @return callable           Current load callback
   */
  protected static function persistenceLoad(callable $callback=null){
    static $retrieve_cb = null;
    return $callback ? $retrieve_cb = $callback : $retrieve_cb;
  }


  /**
   * Enable peristence on `$table` with `$options`
   *   Avaiable options:
   *     `key` : The column name of the primary key, default to `id`.
   *
   * @param  string $table   The table name
   * @param  array $options An associative array with options for the persistance layer.
   * @return void
   */
  public static function persistOn($table, array $options=[]){
    $options['table'] = $table;
    static::persistenceOptions($options);
  }


  /**
   * Override standard save function with a new callback
   * @param  callable $callback The callback to use on model save
   * @return void
   */
  public static function onSave(callable $callback){
    static::persistenceSave($callback);
  }

  /**
   * Override standard load function with a new callback
   * @param  callable $callback The callback to use on model load
   * @return void
   */
  public static function onLoad(callable $callback){
    static::persistenceLoad($callback);
  }

  /**
   * Load the model from the persistence layer
   * @return mixed The retrieved object
   */
  public static function load($pk){
    $table = static::persistenceOptions('table');
    $cb    = static::persistenceLoad();
    $op    = static::persistenceOptions();

    // Use standard persistence on DB layer
    return ( false == is_callable($cb) ) ?
      static::persistenceLoadDefault($pk,$table,$op) : $cb($pk,$table,$op);
  }

  /**
   * Private Standard Load Method
   */
  private static function persistenceLoadDefault($pk, $table, $options){
    if ( $data = SQL::single("SELECT * FROM $table WHERE {$options['key']}=? LIMIT 1",[$pk]) ){
       $obj = new static;
       foreach ((array)$data as $key => $value) {
         $obj->$key = $value;
       }
       if (is_callable(($c=get_called_class())."::trigger")) $c::trigger("load", $obj, $table, $options['key']);
       return $obj;
     } else {
       return null;
     }
  }

  /**
   * Save the model to the persistence layer
   * @return mixed The results from the save callback. (default: lastInsertID)
   */
  public function save(){
    $table  = static::persistenceOptions('table');
    $op     = static::persistenceOptions();
    $cb     = static::persistenceSave();

    // Use standard persistence on DB layer
    $cb = $cb ? Closure::bind($cb, $this) : [$this,'persistenceSaveDefault'];
    return $cb($table,$op);
  }

  /**
   * Private Standard Save Method
   */
  private function persistenceSaveDefault($table,$options){
    if (is_callable(($c=get_called_class())."::trigger")) $c::trigger("save", $this, $table, $options['key']);
    return SQL::insertOrUpdate($table,array_filter((array)$this, function($var) {
      return !is_null($var);
    }),$options['key']);
  }


}
