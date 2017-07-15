<?php

/**
 * Message
 *
 * Pass cross-requests messages.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

class Message extends Dictionary {

  protected static $loaded = false;
  protected static $fields = [];

  /**
   * @return void
   */
  protected static function init(){
    if(false===static::$loaded){
      static::load(Session::get('core.messages',[]));
      static::$loaded = true;
    }
  }

  public static function & get($key,$default=null){
    static::init();
    $value = parent::get($key,'');
    parent::delete($key,'');
    Session::set('core.messages',parent::all());
    return $value;
  }

  /**
   * @return void
   */
  public static function set($key,$data=null){
    static::init();
    parent::set($key,$data);
    Session::set('core.messages',parent::all());
  }

  /**
   * @return void
   */
  public static function add($key,$data=null){
    static::init();
    $d = parent::get($key,[]);
    $d[] = $data;
    parent::set($key,$d);
    Session::set('core.messages',parent::all());
  }

  public static function & all($key=null){
    static::init();
    if($key){
      $all = parent::get($key,[]);
      parent::delete($key);
      Session::set('core.messages',parent::all());
    } else {
      $all = parent::all();
      static::clear();
    }
    return $all;
  }

  /**
   * @return void
   */
  public static function clear(){
    static::init();
    parent::clear();
    Session::delete('core.messages');
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
