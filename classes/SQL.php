<?php

/**
 * SQL
 *
 * SQL database access via PDO.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class SQL {
  use Module;

  protected static $connection = [];
  protected static $pdo        = null;
  protected static $queries    = [];
  
  public static function connect($dsn,$username=null,$password=null,$options=[]){
    static::$connection = [
      'dsn'        => $dsn,
      'username'   => $username,
      'password'   => $password,
      'options'    => array_merge([
        PDO::MYSQL_ATTR_INIT_COMMAND   => "SET NAMES 'UTF8'",
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES     => true,
      ],(array)$options),
    ];
  }

  public static function & connection(){
    if(null === static::$pdo) static::$pdo = new PDO(
          static::$connection['dsn'],
          static::$connection['username'],
          static::$connection['password'],
          static::$connection['options']
      );
    return static::$pdo;
  }
  
  public static function prepare($query){
    return isset(static::$queries[$query]) ? static::$queries[$query] : (static::$queries[$query] = static::connection()->prepare($query));
  }

  public static function exec($query,$params=[]){
    $statement = static::prepare($query);
    $statement->execute($params);
    return $statement;
  }

  public static function value($query,$params=[],$column=0){
    $res = static::exec($query,$params);
    return $res ? $res->fetchColumn($column) : null;
  }  

  public static function each($query,$params=[],callable $looper = null){
    // ($query,$looper) shorthand
    if ($looper===null && is_callable($params)) {$looper = $params; $params = [];}
    if( $res = static::exec($query,$params) ){
      if(is_callable($looper)) 
        while ($row = $res->fetchObject()) $looper($row);
      else
        return $res->fetchAll(PDO::FETCH_CLASS);
    }
  }  

  public static function all($query,$params=[]){
    return static::each($query,$params);
  }  

  public static function delete($table,$pks=null,$pk='id',$comp='IN'){
    if(null===$pks) {
      static::connection()->exec("truncate table `$table`");
    } else {
      if(is_array($pks)){
        $_pks_condition = '(?)'; $_pks = implode(',',$pks);
      } else {
        $_pks_condition = '?'; $_pks = $pks;
      }
      static::exec("delete from `$table` where `$pk` $comp $_pks_condition",[$_pks]);
    }
  }

  public static function insert($table,$data=[]){
    $k = array_keys($data); asort($k);
    $pk = $k; array_walk($pk,function(&$e){ $e = ':'.$e;});
    $data_x = []; array_walk($data,function($e,$key)use(&$data_x){$data_x[':'.$key]=$e;});
    $q = "INSERT into `$table` (`".implode('`,`',$k)."`) VALUES (".implode(',',$pk).")";
    static::exec($q,$data_x);
    return static::connection()->lastInsertId();
  }  

  public static function update($table,$data=[],$pk='id'){
    if(empty($data[$pk])) return false;
    $k = array_keys($data); asort($k);
    array_walk($k,function(&$e){ $e = "`$e`=:$e";});
    $data_x = []; array_walk($data,function($e,$key)use(&$data_x){$data_x[':'.$key]=$e;});
    $q = "UPDATE `$table` SET ".implode(',',$k)." WHERE `$pk`=:$pk";
    static::exec($q,$data_x);
    return static::connection()->lastInsertId();
  }  

  
}
