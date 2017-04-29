<?php

/**
 * SQL
 *
 * SQL database access via PDO.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

class SQL {

  use Module,
      Events,
      Filters;

  protected static $connections = [],
                   $current     = 'default';

  /**
   * Register a new datasource
   * @param  string $name     The assigned name for the datasource
   * @param  string $dsn      PDO DSN URL
   * @param  string $username User credentials
   * @param  string $password User credentials
   * @param  array  $options  Options to pass to the PDO constructor
   * @return SQL\Connection   The datasource resource
   */
  public static function register($name, $dsn, $username=null, $password=null, $options=[]){
    return self::$connections[$name] = new SQL\Connection($dsn, $username, $password, $options);
  }

  /**
   * Register the default datasource
   * @param  string $dsn      PDO DSN URL
   * @param  string $username User credentials
   * @param  string $password User credentials
   * @param  array  $options  Options to pass to the PDO constructor
   * @return SQL\Connection   The datasource resource
   */
  public static function connect($dsn, $username=null, $password=null, $options=[]){
    return self::register('default', $dsn, $username, $password, $options);
  }

  /**
   * Bind the default datasource to another named connection
   * @param  string $name The datasource name
   * @return bool       `true` if correctly changed
   */
  public static function defaultTo($name){
    if (isset(self::$connections[$name])){
      self::$current = $name;
      return true;
    } else return false;
  }

  /**
   * Close one or all (if no parameter passed) registered datasource connections
   * @param  string $name The datasource name, omit for close all of them
   * @return bool       `true` if one or more datasource where closed
   */
  public static function close($name=null){
    if ($name === null) {
      foreach (self::$connections as $conn) $conn->close();
      return true;
    } else if (isset(self::$connections[$name])){
      self::$connections[$name]->close();
      return true;
    } else return false;
  }

  /**
   * Datasource connection accessor
   * @param  string $name The datasource name
   * @return SQL\Connection   The datasource connection
   */
  public static function using($name){
    if (empty(self::$connections[$name]))
      throw new \Exception("[SQL] Unknown connection named '$name'.");
    return self::$connections[$name];
  }

  /**
   * Proxy all direct static calls to the SQL module to the `default` datasource
   * @param  string $method The method name
   * @param  array $args    The method arguments
   * @return mixed          The method return value
   */
  public static function __callStatic($method, $args){
    if (empty(self::$connections[self::$current]))
      throw new \Exception("[SQL] No default connection defined.");
    return call_user_func_array([self::$connections[self::$current],$method],$args);
  }

}


// Default connection to in-memory ephemeral database
SQL::connect('sqlite::memory:');
