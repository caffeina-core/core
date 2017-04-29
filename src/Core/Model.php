<?php

/**
 * Model class
 *
 * Base class for an ORM.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

abstract class Model implements \JsonSerializable {

    use Module,
        Persistence,
        Events,
        Relation;

    public static function where($where_sql = false, $params = [], $flush = false){
      $key = static::persistenceOptions('key');

      return SQL::reduce("SELECT {$key} FROM " . static::persistenceOptions('table') . ($where_sql ? " where {$where_sql}" : ''), $params, function($results, $row) use ($key) {
           $results[] = static::load($row->{$key});
           return $results;
      }, []);
    }

    public static function count($where_sql = false, $params = []) {
      return (int) SQL::value('SELECT COUNT(1) FROM ' . static::persistenceOptions('table') . ($where_sql ? " where {$where_sql}" : ''), $params);
    }

    public static function all($page=1, $limit=-1){
      return static::where($limit < 1 ? "" : "1 limit {$limit} offset " . (max(1,$page)-1)*$limit);
    }

    public function primaryKey(){
      $key = static::persistenceOptions('key');
      return $this->{$key};
    }

    public static function create($data){
      $tmp = new static;
      $data = (object)$data;
      foreach (array_keys(get_object_vars($tmp)) as $key) {
         if (isset($data->{$key})) $tmp->{$key} = $data->{$key};
      }
      $tmp->save();
      static::trigger('create',$tmp,$data);
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
