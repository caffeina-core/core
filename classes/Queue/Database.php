<?php

/**
 * Queue\Database
 *
 * Queue job pool on Database
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Queue;

class Database  {
  protected $table = 'queue';

  public function __construct($options=[]){
    $options = (object)( $options ? $options : [] );
    if (!empty($options->table)) $this->table = $options->table;
    \SQL::exec("CREATE TABLE IF NOT EXISTS `$this->table` (
      `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
      `type`       varchar(128) DEFAULT NULL,
      `status`     enum('PENDING','RUNNING','COMPLETED','ERROR') NOT NULL DEFAULT 'PENDING',
      `priority`   smallint(5) unsigned DEFAULT '0',
      `live_from`  timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `payload`    text,
      PRIMARY KEY (`id`)
    ) DEFAULT CHARSET=utf8");
  }

  public function add($type, $payload, $priority=0, $live_from=0){
    \SQL::insert($this->table,[
      'type'       => $type,
      'priority'   => $priority,
      'live_from'  => $live_from,
      'payload'    => serialize($payload),
      'status'     => 'PENDING',
    ]);
  }

  public function get($id){
    $data = \SQL::row("SELECT FROM `$this->table` WHERE id = ?",[$id]);
    return $data ? new Job($data) : false;
  }

  public function del($id){
    \SQL::update("DELETE FROM `$this->table` WHERE id = ?",[$id]);
  }

  public function update($id, $status){
    \SQL::update($this->table,[
      'id'     => $id,
      'status' => strtoupper($status),
    ]);
  }

  public function next(){
    $data = \SQL::row("SELECT * FROM `$this->table`
                     WHERE `status` = 'PENDING' AND `live_from` < ?
                     ORDER BY `priority` DESC LIMIT 1", [time()]);

    if (empty($data->id)) return false;

    $this->update($data->id, 'RUNNING');
    return new Job($data);
  }

  public static function valid(){
    return true;
  }

  public function all(){
    return \SQL::all("SELECT `id`, `status`
                     FROM `$this->table`
                     WHERE `status` = 'PENDING' AND `live_from` < ?
                     ORDER BY `priority` DESC", [time()]);
  }

}