<?php

/**
 * Email
 *
 * Send messages via Email services.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core;

abstract class Email {

  use Module,
      Events,
      Filters;

  protected static $driver,
                   $options,
                   $driver_name;

  /**
   * @return void
   */
  final public static function using($driver, $options = null){
    if ($driver) {
      $class = __NAMESPACE__ . '\\Email\\' . ucfirst(strtolower($driver));
      if (!class_exists($class)) throw new \Exception("Email driver '$driver' not found.");
      static::$driver_name = $driver;
      static::$options     = $options;
      static::$driver      = new $class;
      static::$driver->onInit($options);
    }
  }

  /**
   * @return \Core\Email\Envelope
   */
  final public static function create($mail=[]){
    return is_a($mail, 'Core\\Email\\Envelope')
           ? $mail
           : new Email\Envelope(array_merge([
              'to'          => false,
              'from'        => false,
              'cc'          => false,
              'bcc'         => false,
              'replyTo'     => false,
              'subject'     => false,
              'message'     => false,
              'attachments' => [],
            ], $mail));
  }

  /**
   * @return bool
   */
  final public static function send($mail){
    $envelope = static::create($mail);
    $results  = (array) static::$driver->onSend($envelope);
    static::trigger('send', $envelope->to(), $envelope, static::$driver_name, $results);
    return count($results) && array_reduce( $results, function($carry, $item) {
      return $carry && $item;
    }, true );
  }

}

Email::using('native');
