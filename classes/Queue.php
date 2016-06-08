<?php

/**
 * Queue
 *
 * Manage a job queue.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class Queue {
  use Module;

  protected static $driver = null;

   /**
     * Load queue drivers with a FCFS strategy
     *
     * @method using
     * @param  mixed $driver can be a single driver name string, an array of driver names or a map [ driver_name => driver_options array ]
     * @return bool   true if a driver was loaded
     * @example
     *
     *   Queue::using('database');
     *   Queue::using(['database','files']); // Prefer "database" over "files"
     *   Queue::using([
     *         'database',
     *         'files' => [
     *             'path'   => './jobs',
     *          ],
     *   ]);
     *
     */
    public static function using($driver){
      foreach((array)$driver as $key => $value){
          if(is_numeric($key)){
            $drv = $value; $conf = [];
          } else {
            $drv = $key;   $conf = $value;
          }
          $class = 'Queue\\' . ucfirst(strtolower($drv));
          if(class_exists($class) && $class::valid()) {
            static::$driver = new $class($conf);
            return true;
          }
        }
       return false;
    }

    public static function add($type, $payload, $priority=0, $live_from=0){
      return static::$driver->add($type,serialize($payload),max(0,$priority),$live_from?strtotime($live_from):0);
    }

    public static function get($id){
      $job = static::$driver->add($id);
    }

    public static function del($id){

    }

    public static function update($id, $status){

    }

    public static function next(){

    }

    public static function all(){

    }

    public static function valid(){

    }

}


Queue::using([
  'database',
  'files' => [
    'path' => Options::get('core.queue.files.path','sys_get_temp_dir'),
  ],
]);
