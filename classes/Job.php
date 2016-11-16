<?php

/**
 * Job
 *
 * Simple, database based job queue.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2016 - http://caffeina.com
 */

/*

Here is the table needed :

CREATE TABLE `queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) DEFAULT NULL,
  `status` enum('PENDING','ACTIVE','DONE','ERROR') NOT NULL DEFAULT 'PENDING',
  `tries` int(5) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scheduled_at` timestamp NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `payload` text,
  `error` text,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `scheduled_at` (`scheduled_at`)
) CHARSET=utf8;


*/

class Job extends Model {
  const _PRIMARY_KEY_ = "queue.id";
  private static $sql_connection = 'default';

  public $id,
         $type,
         $status = 'PENDING',
         $tries = 0,
         $created_at,
         $scheduled_at,
         $activated_at,
         $payload,
         $error;

  public static function queue($type, $payload=null, $when=null){
    $now = gmdate("Y-m-d H:i:s");
    $job = new static;
    $job->type         = $type;
    $job->created_at   = $now;
    $job->scheduled_at = $when ? gmdate("Y-m-d H:i:s",(is_int($when) ? $when : (strtotime($when)?:time()))) : $now;
    $job->payload      = $payload !== null ? serialize($payload) : null;
    $job->save();
  }

  public static function register($type, $callback){
    self::on("worker[{$type}]", $callback);
  }

  public static function cleanQueue($all=false){
    $statuses = $all ? "'DONE','ERROR'" : "'DONE'";
    return SQL::using(static::$sql_connection)->exec("DELETE FROM `".static::persistenceOptions('table')."` WHERE `status` IN ($statuses)");
  }

  public static function execute(){
    $condition = "status = 'PENDING' and `scheduled_at` <= NOW() ORDER BY `scheduled_at` ASC LIMIT 1";
    if (($job = static::where($condition)) && ($job = current($job))){
      // Lock chosen job rapidly
      $table = static::persistenceOptions('table');
      SQL::exec("LOCK TABLES `$table` WRITE");
      SQL::update($table,['id' => $job->id, 'status' => 'ACTIVE']);
      SQL::exec("UNLOCK TABLES");
      $job->run();
      return $job;
    } else return false;
  }

  public function run(){
    $this->status = 'ACTIVE';
    $this->activated_at = gmdate("Y-m-d H:i:s");
    $this->tries++;
    $this->save();
    $this->status = 'DONE';
    self::trigger("worker[{$this->type}]", $this, $this->payload ? unserialize($this->payload) : null);
    $this->save();
  }

  public function error($message=null){
    $this->status = 'ERROR';
    $this->error  = $message;
  }

  public function retry($message=null){
    $this->status = 'PENDING';
    $this->error  = $message;
  }

}
