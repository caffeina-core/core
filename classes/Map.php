<?php

/**
 * Map class
 *
 * The map class allow to handle a repository of key-values data.
 * Values are accessibles via a dot notation key path.
 * 
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class Map implements JsonSerializable {

    protected $fields = [];

    /**
     * Returns the map as an associative array
     * @return array reference
     */
    public function & all(){
        return $this->fields;
    }

    /**
     * Get a value assigned to a key path from the map
     * @param  string $key The key path of the value in dot notation 
     * @param  mixed $default (optional) The default value. If is a callable it will executed and the return value will be used.
     * @return mixed The value of the key or the default (resolved) value if the key not existed.
     */
    public function get($key, $default=null){
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
     * Set a value for a key path from map
     * @param  string $key The key path of the value in dot notation 
     * @param  mixed $value (optional) The value. If is a callable it will executed and the return value will be used.
     * @return mixed The value of the key or the default (resolved) value if the key not existed.
     */
    public function set($key, $value=null){
        if (is_array($key)) {
            return static::merge($key);
        } else {
            $ptr = & static::find($key,true);
            return $ptr = $value;
        }
    }

    /**
     * Delete a value and the key path from map.
     * @param  string $key The key path in dot notation 
     * @param  boolean $compact (optional) Compact map removing empty paths.
     */
    public function delete($key, $compact=true){
        static::set($key,null);
        if ($compact) static::compact();
    }

    /**
     * Check if a key path exists in map.
     * @param  string $key The key path in dot notation 
     * @return boolean
     */
    public function exists($key){
        return null !== static::find($key,false);
    }

    /**
     * Clear all key path in map.
     */
    public function clear(){
        $this->fields = [];
    }

    public function __construct($fields=null){
        $this->load($fields);
    }
    
    /**
     * Load an associative array/object as the map source.
     * @param  string $fields The array to merge
     */
    public function load($fields){
        if ($fields) $this->fields = (array)$fields;
    }

    /**
     * Merge an associative array to the map. 
     * @param  array   $array The array to merge
     * @param  boolean $merge_back If `true` merge the map over the $array, if `false` (default) the reverse.
     */
    public function merge(array $array, $merge_back=false){
        $this->fields = $merge_back
            ? array_replace_recursive($array, $this->fields)
            : array_replace_recursive($this->fields, $array);
    }

    /**
     * Compact map removing empty paths
     */
    protected function compact(){
        function array_filter_rec($input, $callback = null) { 
            foreach ($input as &$value) { 
                if (is_array($value)) { 
                    $value = array_filter_rec($value, $callback); 
                } 
            } 
            return array_filter($input, $callback); 
        } 

        $this->fields = array_filter_rec($this->fields,function($a){ return $a !== null; });
    }

    /**
     * Navigate map and find the element from the path in dot notation.
     * @param  string  $path Key path in dot notation.
     * @param  boolean $create If true will create empty paths.
     * @param  callable  If passed this callback will be applied to the founded value.
     * @return mixed The founded value.
     */
    protected static function & find($path, $create=false, callable $operation=null) {
        $tok = strtok($path,'.');        
        if($create){
            $value =& $this->fields;
        } else {
            $value = $this->fields;
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
      return $this->fields;
    }


}
