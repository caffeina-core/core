<?php

/**
 * Dictionary class
 *
 * The dictionary trait allow to handle a repository of key-values data
 * Values are accessibles via a dot notation key path.
 * Must be used by class hierarchy.
 * 
 * Example:
 * <code>
 *  class MyConfig extends Dictionary {}
 *  MyConfig::set('user',[ 'name' => 'Frank', 'surname' => 'Castle' ]);
 *  echo "Hello, my name is ",MyConfig::get('user.name'),' ',MyConfig::get('user.surname');
 * </code>
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

abstract class Dictionary implements JsonSerializable {
    use Module;

    protected static $fields = [];

    /**
     * Returns the map as an associative array
     * @return array reference
     */
    public static function & all(){
        return static::$fields;
    }

    /**
     * Get a value assigned to a key path from the dictionary
     * @param  string $key The key path of the value in dot notation 
     * @param  mixed $default (optional) The default value. If is a callable it will executed and the return value will be used.
     * @return mixed The value of the key or the default (resolved) value if the key not existed.
     */
    public static function get($key,$default=null){
        if ($ptr = & static::find($key,false)){
            return $ptr;
        } else {
            if ($default !== null){
                return static::set($key,is_callable($default)?call_user_func($default):$default);
            } else {
                return null;
            }
        }
    }

    /**
     * Set a value for a key path from dictionary
     * @param  string $key The key path of the value in dot notation 
     * @param  mixed $value (optional) The value. If is a callable it will executed and the return value will be used.
     * @return mixed The value of the key or the default (resolved) value if the key not existed.
     */
    public static function set($key,$value=null){
        if (is_array($key)) {
            return static::merge($key);
        } else {
            $ptr = & static::find($key,true);
            return $ptr = $value;
        }
    }

    /**
     * Delete a value and the key path from dictionary.
     * @param  string $key The key path in dot notation 
     * @param  boolean $compact (optional) Compact dictionary removing empty paths.
     */
    public static function delete($key,$compact=true){
        static::set($key,null);
        if ($compact) static::compact();
    }

    /**
     * Check if a key path exists in dictionary.
     * @param  string $key The key path in dot notation 
     * @return boolean
     */
    public static function exists($key){
        return static::find($key,false) !== null;
    }

    /**
     * Clear all key path in dictionary.
     */
    public static function clear(){
        static::$fields = [];
    }
    
    /**
     * Load an associative array/object as the dictionary source.
     * @param  string $fields The array to merge
     */
    public static function load($fields){
        if($fields) static::$fields = (array)$fields;
    }

    /**
     * Merge an associative array to the dictionary. 
     * @param  array   $array The array to merge
     * @param  boolean $merge_back If `true` merge the dictionary over the $array, if `false` (default) the reverse.
     */
    public static function merge(array $array,$merge_back=false){
        static $array_merge_recursive_distinct = null;
        if ($array_merge_recursive_distinct == null)
        $array_merge_recursive_distinct = function() use (&$array_merge_recursive_distinct){
          $arrays = func_get_args();
          $base = array_shift($arrays);
          if(!is_array($base)) $base = empty($base) ? array() : array($base);
          foreach($arrays as $append) {
            if(!is_array($append)) $append = array($append);
            foreach($append as $key => $value) {
              if(!array_key_exists($key, $base) and !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
              }
              if(is_array($value) or is_array($base[$key])) {
                $base[$key] = $array_merge_recursive_distinct($base[$key], $append[$key]);
              } else if(is_numeric($key)) {
                if(!in_array($value, $base)) $base[] = $value;
              } else {
                $base[$key] = $value;
              }
            }
          }
          return $base;
        };

        $fields = $merge_back ? 
            $array_merge_recursive_distinct($array,static::$fields)
            :
            $array_merge_recursive_distinct(static::$fields,$array);
        static::$fields = $fields;
    }

    /**
     * Compact dictionary removing empty paths
     */
    protected static function compact(){
        function array_filter_rec($input, $callback = null) { 
            foreach ($input as &$value) { 
                if (is_array($value)) { 
                    $value = array_filter_rec($value, $callback); 
                } 
            } 
            return array_filter($input, $callback); 
        } 

        static::$fields = array_filter_rec(static::$fields,function($a){ return $a !== null; });
    }

    /**
     * Navigate dictionary and find the element from the path in dot notation.
     * @param  string  $path Key path in dot notation.
     * @param  boolean $create If true will create empty paths.
     * @param  callable  If passed this callback will be applied to the founded value.
     * @return mixed The founded value.
     */
    protected static function & find($path,$create=false,callable $operation=null) {
        $tok = strtok($path,'.');        
        if($create){
            $value = & static::$fields;
        } else {
            $value = static::$fields;
        }
        while($tok !== false){
            $value = & $value[$tok];
            $tok = strtok('.');
        }
        is_callable($operation) ? $operation($value) : '';
        return $value;
    }

    /**
     * JsonSerializable Interface handler
     *
     * @method jsonSerialize
     *
     * @return string        The json object
     */
    public function jsonSerialize(){
      return static::$fields;
    }


}
