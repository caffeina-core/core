<?php

/**
 * Email
 *
 * Send messages via Email services.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */


class Email {
  protected static $driver = null;

  protected static function instance(){
    return new static::$driver();
  }

  public static function using($driver){
    static::$driver = '\\Email\\'.ucfirst(strtolower($driver));
  }

  protected static function get_email_parts($value){
    if(strpos($value,'<')!==false){
      $value = str_replace('>','',$value);
      $parts = explode('<',$value,2);
      $name = trim(current($parts));
      $email = trim(end($parts));
      return (object) [
        'name' => $name,
        'email' => $email,
      ];
    } else {
      return (object) [
        'name' => '',
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


/**
 * EmailInterface
 *
 * Email services common interface.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

interface EmailInterface  {
  public function addAddress($email,$name='');
  public function from($email,$name='');
  public function replyTo($email,$name='');
  public function subject($text);
  public function message($text);
  public function addAttachment($file);
  public function send();
}

Email::using('native');
