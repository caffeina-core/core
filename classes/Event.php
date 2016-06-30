<?php

/**
 * Event
 *
 * Generic event emitter-listener.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

class Event {
    use Module, Events;

    public static function single($name,callable $listener){
        return static::onSingle($name,$listener);
    }
}
