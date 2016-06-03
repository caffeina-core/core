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
  public function start();
  public function add($type, $payload, $priority=0, $enable_at=0);
  public function get($id);
  public function del($id);
  public function update($id, $status);
  public function list();
}