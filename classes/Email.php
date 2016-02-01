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

  protected static $driver;

  protected static function instance(){
    return static::$driver;
  }

  public static function using($driver, $options = null){
    $class = '\\Email\\'.ucfirst(strtolower($driver));
    if ( ! class_exists($class) ) throw new \Exception("[Core.Email] : $driver driver not found.");
    static::$driver = new $class($options);
  }

  protected static function get_email_parts($value){
    if(strpos($value,'<')!==false){
      $value = str_replace('>','',$value);
      $parts = explode('<',$value,2);
      $name  = trim(current($parts));
      $email = trim(end($parts));
      return (object) [
        'name'  => $name,
        'email' => $email,
      ];
    } else {
      return (object) [
        'name'  => '',
        'email' => $value,
      ];
    }
  }

  public static function send(array $options){
    $mail = static::instance();

    $options = array_merge([
      'to'          => '',
      'from'        => '',
      'replyTo'     => '',
      'subject'     => '',
      'message'     => '',
      'attachments' => [],
    ],$options);

    // To
    foreach((array)$options['to'] as $value){
      $to = static::get_email_parts($value);
      empty($to->name)
        ? $mail->addAddress($to->email)
        : $mail->addAddress($to->email,$to->name);
    }

    // From
    $from = static::get_email_parts($options['from']);
    empty($from->name)
      ? $mail->from($from->email)
      : $mail->from($from->email,$from->name);

    // Reply
    $replyTo = static::get_email_parts($options['replyTo']);
    empty($replyTo->name)
      ? $mail->replyTo($replyTo->email)
      : $mail->replyTo($replyTo->email,$replyTo->name);

    // Subjects
    $mail->subject($options['subject']);

    // Message
    $mail->message($options['message']);

    // Attachments
    foreach((array)$options['attachments'] as $value){
      $mail->addAttachment($value);
    }

    return $mail->send();
  }
}

Email::using('native');
