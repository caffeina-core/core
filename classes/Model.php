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

abstract class Model {
    use Module, Persistence;

    public static function where($where_sql = false){
        // Forward persistence calls to caller class, not Model
        $self    = get_called_class();
        $table   = $self::persistenceOptions('table');
        $key     = $self::persistenceOptions('key');

        $sql = "select $key from $table" . ($where_sql ? " where $where_sql" : '');

        $results = [];
        SQL::each($sql, function($row) use ($self,&$results){
            $results[] = $self::load($row->id);
        });
        return $results;
    }

    public static function all($page=1,$limit=-1){
        $offset = max(1,$page)-1;
        return static::where($limit < 1 ? "" : "1 limit $limit offset $offset");
    }

    public static function create(array $data){
        $tmp = new static;
        foreach ($data as $key => $value) {
           $tmp->$key = $value;
        }
        $tmp->save();
        return $tmp;
    }

}
