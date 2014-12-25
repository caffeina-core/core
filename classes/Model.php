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

class Model {
    use Module, Persistence;

    public static function all(){

        $table   = static::persistenceOptions('table');
        $key     = static::persistenceOptions('key');

        if (!$table) return [];

        $results = [];
        SQL::each("select $key from $table", function($row) use (&$results){
            $results[] = static::load($row->id);
        });
        return $results;
    }
}
