<?php

/**
 * Dictionary class
 *
 * The dictionary class allow to handle a repository of key-values data
 * Values are accessibles via a dot notation key path.
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
 * @copyright Caffeina srl - 2015 - http://caffeina.it
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
    public static function get($key, $default=null){
        if ($ptr =& static::find($key,false)){
            return $ptr;
        } else {
            if ($default !== null){
                return static::set($key, is_callable($default) ? call_user_func($default) : $default);
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
    public static function set($key, $value=null){
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
    public static function delete($key, $compact=true){
        static::set($key,null);
        if ($compact) static::compact();
    }

    /**
     * Check if a key path exists in dictionary.
     * @param  string $key The key path in dot notation 
     * @return boolean
     */
    public static function exists($key){
        return null !== static::find($key,false);
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
        if ($fields) static::$fields = (array)$fields;
    }

    /**
     * Merge an associative array to the dictionary. 
     * @param  array   $array The array to merge
     * @param  boolean $merge_back If `true` merge the dictionary over the $array, if `false` (default) the reverse.
     */
    public static function merge(array $array, $merge_back=false){
        static::$fields = $merge_back
            ? array_replace_recursive($array, static::$fields)
            : array_replace_recursive(static::$fields, $array);
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
    protected static function & find($path, $create=false, callable $operation=null) {
        $tok = strtok($path,'.');        
        if($create){
            $value =& static::$fields;
        } else {
            $value = static::$fields;
        }
        while($tok !== false){
            $value =& $value[$tok];
            $tok = strtok('.');
        }
        if (is_callable($operation)) $operation($value);
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
