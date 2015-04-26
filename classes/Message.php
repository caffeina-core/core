<?php

/**
 * Message
 *
 * Pass cross-requests messages.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Message extends Dictionary {

  protected static $loaded = false;
  protected static $fields = [];

  protected static function init(){
    if(false===static::$loaded){
      static::load(Session::get('_messages',[]));
      static::$loaded = true;
    }
  }

  public static function & get($key,$default=null){
    static::init();
    $value = parent::get($key,'');
    parent::delete($key,'');
    Session::set('_messages',parent::all());
    return $value;
  }

  public static function set($key,$data=null){
    static::init();
    parent::set($key,$data);
    return Session::set('_messages',parent::all());
  }

  public static function add($key,$data=null){
    static::init();
    $d = parent::get($key,[]);
    $d[] = $data;
    parent::set($key,$d);
    return Session::set('_messages',parent::all());
  }

  public static function & all($key=null){
    static::init();
    if($key){
      $all = parent::get($key,[]);
      parent::delete($key);
      Session::set('_messages',parent::all());
    } else {
      $all = parent::all();
      static::clear();
    }
    return $all;
  }

  public static function clear(){
    static::init();
    parent::clear();
    Session::delete('_messages');
  }


  /**
   * Return a read-only accessor to messages variables for in-view use.
   * @return MessageReadOnly
   */
  public static function readOnly(){
    return new MessageReadOnly();
  }

}  /* End of class */


/**
 * Read-only Message accessor class
 */

class MessageReadOnly {

  public function __get($key){
    return Message::get($key);
  }
  
  public function __isset($key){
    return true;
  }

}  /* End of class */
