<?php

/**
 * Event
 *
 * Generic global event emitter-listener.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace Core;

abstract class Event {
  use Module,
      Events;

  final public static function single($name,callable $listener){
    return static::onSingle($name,$listener);
  }
}
