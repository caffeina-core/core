<?php

/**
 * Email
 *
 * Send messages via Email services.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Email {
  use Module;

  protected static $driver,
                   $options,
                   $driver_name;

  protected static function instance(){
    return static::$driver;
  }

  public static function using($driver, $options = null){
    $class = '\\Email\\'.ucfirst(strtolower($driver));
    if ( ! class_exists($class) ) throw new \Exception("[Core.Email] : $driver driver not found.");
    static::$driver_name = $driver;
    static::$options = $options;
    static::$driver = new $class($options);
  }

  public static function clear(){
    static::using( static::$driver_name, static::$options );
  }

  public static function send(array $options){
    $mail = static::instance();

    $options = array_merge([
      'to'          => false,
      'from'        => false,
      'replyTo'     => false,
      'subject'     => false,
      'message'     => false,
      'attachments' => [],
    ],$options);

    return $mail->send(new Envelope($option));
  }

}

Email::using('native');
