<?php

/**
 * SQL
 *
 * SQL database access via PDO.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */


class SQL {
  use Module;
  protected static $connections = [],
                   $current     = 'default';

  /**
   * Register a new datasource
   * @param  string $name     The assigned name for the datasource
   * @param  string $dsn      PDO DSN URL
   * @param  string $username User credentials
   * @param  string $password User credentials
   * @param  array  $options  Options to pass to the PDO constructor
   * @return SQLConnection    The datasource resource
   */
  public static function register($name, $dsn, $username=null, $password=null, $options=[]){
    return self::$connections[$name] = new SQLConnection($dsn, $username, $password, $options);
  }

  /**
   * Register the default datasource
   * @param  string $dsn      PDO DSN URL
   * @param  string $username User credentials
   * @param  string $password User credentials
   * @param  array  $options  Options to pass to the PDO constructor
   * @return SQLConnection    The datasource resource
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
   * @param  strinf $name The datasource name
   * @return SQLConnect   The datasource connection
   */
  public static function using($name){
    if (empty(self::$connections[$name])) throw new \Exception("[SQL] Unknown connection named '$name'.");
    return self::$connections[$name];
  }

  /**
   * Proxy all direct static calls to the SQL module to the `default` datasource
   * @param  string $method The method name
   * @param  array $args    The method arguments
   * @return mixed          The method return value
   */
  public static function __callStatic($method, $args){
    if (empty(self::$connections[self::$current])) throw new \Exception("[SQL] No default connection defined.");
    return call_user_func_array([self::$connections[self::$current],$method],$args);
  }

}

// Default connection to in-memory ephemeral database
SQL::connect('sqlite::memory:');


class SQLConnection {

  protected $connection        = [],
            $queries           = [],
            $last_exec_success = true;

  public function __construct($dsn, $username=null, $password=null, $options=[]){
    $this->connection = [
      'dsn'        => $dsn,
      'pdo'        => null,
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
      $this->connection['options'] = $options;
    }
  }

  public function close(){
    $this->connection['pdo'] = null;
  }

  public function connection(){
    if(empty($this->connection['pdo'])) {
      try {
        $this->connection['pdo'] = new PDO(
            $this->connection['dsn'],
            $this->connection['username'],
            $this->connection['password'],
            $this->connection['options']

        );
        Event::trigger('core.sql.connect',$this);
      } catch(Exception $e) {
        $this->connection['pdo'] = null;
      }
    }
    return $this->connection['pdo'];
  }

  public function prepare($query){
    if(!$this->connection()) return false;
    return isset($this->queries[$query]) ? $this->queries[$query] : ($this->queries[$query] = $this->connection()->prepare($query));
  }

  public function exec($query, $params=[]){
    if(!$this->connection()) return false;

    if (false==is_array($params)) $params = (array)$params;
    $query = Filter::with('core.sql.query',$query);
    if($statement = $this->prepare($query)){
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

    $this->last_exec_success = $statement && $statement->execute();
    return $statement;
  }

  public function value($query, $params=[], $column=0){
    if(!$this->connection()) return false;

    $res = $this->exec($query,$params);
    return $res ? $res->fetchColumn($column) : null;
  }

  public function each($query, $params=[], callable $looper = null){
    if(!$this->connection()) return false;

    // ($query,$looper) shorthand
    if ($looper===null && is_callable($params)) {$looper = $params; $params = [];}
    if( $res = $this->exec($query,$params) ){
      if(is_callable($looper))
        while ($row = $res->fetchObject()) $looper($row);
      else
        return $res->fetchAll(PDO::FETCH_CLASS);
    }
  }

  public function single($query, $params=[], callable $handler = null){
    if(!$this->connection()) return false;

    // ($query,$handler) shorthand
    if ($handler===null && is_callable($params)) {$handler = $params; $params = [];}
    if( $res = $this->exec($query,$params) ){
        if (is_callable($handler))
          $handler($res->fetchObject());
        else
          return $res->fetchObject();
    }
  }

 public function run($script){
    if(!$this->connection()) return false;

    $sql_path = Options::get('database.sql.path',APP_DIR.'/sql');
    $sql_sep  = Options::get('database.sql.separator',';');
    if (is_file($f = "$sql_path/$script.sql")){
        $result = true;
        foreach(explode($sql_sep,file_get_contents($f)) as $statement) {
            $result = $this->exec($statement);
        }
        return $result;
    } else return false;
  }

  public function all($query, $params=[], callable $looper = null){
   if(!$this->connection()) return false;
   return $this->each($query,$params,$looper);
  }

  public function delete($table, $pks=null, $pk='id', $inclusive=true){
    if(!$this->connection()) return false;

    if (null===$pks) {
      return $this->exec("DELETE FROM `$table`");
    } else {
      return $this->exec("DELETE FROM `$table` WHERE `$pk` ".($inclusive ? "" : "NOT " )."IN (" . implode( ',', array_fill_keys( (array)$pks, '?' ) ) . ")",(array)$pks);
    }
  }

  public function insert($table, $data){
    if(!$this->connection()) return false;

    if (false==is_array($data)) $data = (array)$data;
    $k = array_keys($data);
    asort($k);
    $pk = $k;
    array_walk($pk,function(&$e){ $e = ':'.$e;});
    $q = "INSERT INTO `$table` (`".implode('`,`',$k)."`) VALUES (".implode(',',$pk).")";
    $this->exec($q,$data);
    return $this->last_exec_success ? $this->connection()->lastInsertId() : false;
  }

  public function updateWhere($table, $data, $where, $pk='id'){
    if(!$this->connection()) return false;

    if (false==is_array($data)) $data = (array)$data;
    if (empty($data)) return false;
    $k = array_keys($data);
    asort($k);

    // Remove primary key from SET
    array_walk($k,function(&$e) use ($pk) {
      $e = ($e==$pk) ? null : "`$e`=:$e";
    });

    $q = "UPDATE `$table` SET ".implode(', ',array_filter($k))." WHERE $where";
    $this->exec($q,$data);
    return $this->last_exec_success;
  }

  public function update($table, $data, $pk='id', $extra_where=''){
    return $this->updateWhere($table, $data, "`$pk`=:$pk $extra_where", $pk);
  }

  public function insertOrUpdate($table, $data=[], $pk='id', $extra_where=''){
    if(!$this->connection()) return false;

    if (false==is_array($data)) $data = (array)$data;
    if (empty($data[$pk])) return $this->insert($table, $data);
    if( (string) $this->value("SELECT `$pk` FROM `$table` WHERE `$pk`=? LIMIT 1", [$data[$pk]]) === (string) $data[$pk] ){
        return $this->update($table, $data, $pk, $extra_where);
    } else {
        return $this->insert($table, $data);
    }
  }
}

