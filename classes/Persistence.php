<?php

/**
 * Persistence trait
 *
 * Provides a way to persist a class on a Database.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
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
  protected static $__persistence__ = [];
  protected static function persistenceOptions($options=null){
    if ($options === null) return static::$__persistence__;
    if (is_array($options)) {
      return static::$__persistence__ = $options;
    } else {
      return isset(static::$__persistence__[$options]) ? static::$__persistence__[$options] : '';
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
    $options = array_merge($options,[
      'key' => 'id'
    ]);
    $options['table'] = $table;
    static::$__persistence__ = $options;
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
    $op = static::$__persistence__;
    $cb = static::persistenceLoad();
    // Use standard persistence on DB layer
    return ( false == is_callable($cb) ) ? static::persistenceLoadDefault($pk,$op['table'],$op) : $cb($pk,$op['table'],$op);
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
    $op = static::$__persistence__;
    $cb = static::persistenceSave();
    // Use standard persistence on DB layer
    $cb = $cb ? Closure::bind($cb,$this) : [$this,'persistenceSaveDefault'];
    return $cb($op['table'],$op);
  }

  /**
   * Private Standard Save Method
   */
  private function persistenceSaveDefault($table,$options){
     return SQL::insertOrUpdate($table,array_filter((array)$this),$options['key']);    
  }

}
