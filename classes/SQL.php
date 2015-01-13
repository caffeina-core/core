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
  protected static $last_exec_success = true;

  public static function connect($dsn, $username=null, $password=null, $options=[]){
    static::$connection = [
      'dsn'        => $dsn,
      'username'   => $username,
      'password'   => $password,
      'options'    => array_merge([
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES     => true,
      ],$options),
    ];
    // "The auto-commit mode cannot be changed for this driver" SQLite workaround
    if (strpos($dsn,'sqlite:') === 0) {
      static::$connection['options'] = $options;
    }
  }

  public static function & connection(){
    if(null === static::$pdo) {
      static::$pdo = new PDO(
          static::$connection['dsn'],
          static::$connection['username'],
          static::$connection['password'],
          static::$connection['options']

      );
      Event::triggerOnce('core.sql.connect');
    }
    return static::$pdo;
  }

  public static function prepare($query){
    return isset(static::$queries[$query]) ? static::$queries[$query] : (static::$queries[$query] = static::connection()->prepare($query));
  }

  public static function exec($query, $params=[]){
    if (false==is_array($params)) $params = (array)$params;
    $query = Filter::with('core.sql.query',$query);
    if($statement = static::prepare($query)){
      Event::trigger('core.sql.query',$query,$params,(bool)$statement);

      foreach ($params as $key => $val) {
        $type = PDO::PARAM_STR;
        if (is_bool($val)) {
          $type = PDO::PARAM_BOOL;
        } elseif (is_null($val)) {
          $type = PDO::PARAM_NULL;
        } elseif (is_int($val)) {
          $type = PDO::PARAM_INT;
        }
        // bindValue need a 1-based numeric parameter
        $statement->bindValue(is_numeric($key)?$key+1:':'.$key, $val, $type);
      }
    } else {
      Event::trigger('core.sql.error',$query,$params);
      return false;
    }

    static::$last_exec_success = $statement && $statement->execute();
    return $statement;
  }

  public static function value($query, $params=[], $column=0){
    $res = static::exec($query,$params);
    return $res ? $res->fetchColumn($column) : null;
  }

  public static function each($query, $params=[], callable $looper = null){
    // ($query,$looper) shorthand
    if ($looper===null && is_callable($params)) {$looper = $params; $params = [];}
    if( $res = static::exec($query,$params) ){
      if(is_callable($looper))
        while ($row = $res->fetchObject()) $looper($row);
      else
        return $res->fetchAll(PDO::FETCH_CLASS);
    }
  }

  public static function single($query, $params=[], callable $handler = null){
    // ($query,$handler) shorthand
    if ($handler===null && is_callable($params)) {$handler = $params; $params = [];}
    if( $res = static::exec($query,$params) ){
        if (is_callable($handler))
          $handler($res->fetchObject());
        else
          return $res->fetchObject();
    }
  }

 public static function run($script){
    $sql_path = Options::get('database.sql.path',APP_DIR.'/sql');
    $sql_sep  = Options::get('database.sql.separator',';');
    if (is_file($f = "$sql_path/$script.sql")){
        $result = true;
        foreach(explode($sql_sep,file_get_contents($f)) as $statement) {
            $result = SQL::exec($statement);
        }
        return $result;
    } else return false;
  }

  public static function all($query, $params=[]){
    return static::each($query,$params);
  }

  public static function delete($table, $pks=null, $pk='id', $inclusive=true){
    if (null===$pks) {
      return static::exec("DELETE FROM `$table`");
    } else {
      return static::exec("DELETE FROM `$table` WHERE `$pk` ".($inclusive ? "" : "NOT " )."IN (?)",[
           implode(',',(array)$pks)
      ]);
    }
  }

  public static function insert($table, $data=[]){
    if (false==is_array($data)) $data = (array)$data;
    $k = array_keys($data);
    asort($k);
    $pk = $k;
    array_walk($pk,function(&$e){ $e = ':'.$e;});
    $q = "INSERT INTO `$table` (`".implode('`,`',$k)."`) VALUES (".implode(',',$pk).")";
    static::exec($q,$data);
    return static::$last_exec_success ? static::connection()->lastInsertId() : false;
  }

  public static function update($table, $data=[], $pk='id', $extra_where=''){
    if (false==is_array($data)) $data = (array)$data;
    if (empty($data[$pk])) return false;
    $k = array_keys($data);
    asort($k);
    array_walk($k,function(&$e){ $e = "`$e`=:$e";});
    $q = "UPDATE `$table` SET ".implode(', ',$k)." WHERE `$pk`=:$pk $extra_where";
    static::exec($q,$data);
    return static::$last_exec_success;
  }

  public static function insertOrUpdate($table, $data=[], $pk='id', $extra_where=''){
    if (false==is_array($data)) $data = (array)$data;
    if (empty($data[$pk])) return static::insert($table, $data);
    if( (string) static::value("SELECT `$pk` FROM `$table` WHERE `$pk`=? LIMIT 1", [$data[$pk]]) === (string) $data[$pk] ){
        return static::update($table, $data, $pk, $extra_where);
    } else {
        return static::insert($table, $data);
    }
  }

}
