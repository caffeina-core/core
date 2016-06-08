<?php

/**
 * Queue\Adapter
 *
 * Queue job pools common interface.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Queue;

interface Adapter  {
  public function __construct($options=[]);
  public function add($type, $payload, $priority=0, $live_from=0);
  public function get($id);
  public function del($id);
  public function update($id, $status);
  public function next();
  public function all();
  public function valid();
}