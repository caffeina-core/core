<?php

/**
 * class Model
 *
 * Base for an ORM.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

abstract class Model {
    use Module, Persistence;

    public static function where($where_sql = false){
        $self = get_called_class();

        $table   = $self::persistenceOptions('table');
        $key     = $self::persistenceOptions('key');

        if (!$table) return [];

        $sql = "select $key from $table" . ($where_sql ? " where $where_sql" : '');

        $results = [];
        SQL::each($sql, function($row) use ($self,&$results){
            $results[] = $self::load($row->id);
        });
        return $results;
    }


    public static function all(){
        return static::where();
    }

}
