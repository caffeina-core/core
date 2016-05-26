<?php

/**
 * Model class
 *
 * Base class for an ORM.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

abstract class Model implements JsonSerializable {
    use Module, Persistence, Relation;

    public static function where($where_sql = false){
      // Forward persistence calls to caller class, not Model
      $self  = get_called_class();
      $table = $self::persistenceOptions('table');
      $key   = $self::persistenceOptions('key');

      $sql   = "select $key from $table" . ($where_sql ? " where $where_sql" : '');

      $results = [];
      SQL::each($sql, function($row) use ($self, &$results, $key){
          $results[] = $self::load($row->$key);
      });
      return $results;
    }

    public static function all($page=1, $limit=-1){
      $offset = max(1,$page)-1;
      return static::where($limit < 1 ? "" : "1 limit $limit offset $offset");
    }

    public function primaryKey(){
      $self = get_called_class();
      $key  = $self::persistenceOptions('key');
      return $this->$key;
    }

    public static function create($data){
      $tmp = new static;
      foreach ((array)$data as $key => $value) {
         $tmp->$key = $value;
      }
      $tmp->save();
      return $tmp;
    }

    public function export($transformer=null, $disabled_relations=[]){
      $data = [];
      if (!is_callable($transformer)) $transformer = function($k,$v){ return [$k=>$v]; };
      foreach (get_object_vars($this) as $key => $value) {
        if ($res = $transformer($key, $value)){
          $data[key($res)] = current($res);
        }
      }

      foreach (static::relationOptions()->links as $hash => $link) {
        $relation = $link->method;
        // Expand relations but protect from self-references loop
        if (isset($disabled_relations[$hash])) continue;
        $disabled_relations[$hash] = true;
        $value = $this->$relation;
        if ($value && is_array($value))
          foreach ($value as &$val) $val = $val->export(null,$disabled_relations);
        else
          $value = $value ? $value->export(null,$disabled_relations) : false;
        unset($disabled_relations[$hash]);

        if ($res = $transformer($relation, $value)){
          $data[key($res)] = current($res);
        }

      }
      return $data;
    }

    public function jsonSerialize() {
      return $this->export();
    }

}
