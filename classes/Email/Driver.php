<?php

/**
 * Email\Driver
 *
 * Email services common interface.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0.0
 * @copyright Caffeina srl - 2015 - http://caffeina.co
 */

namespace Email;

interface Driver  {
  public function addAddress($email,$name='');
  public function from($email,$name='');
  public function replyTo($email,$name='');
  public function subject($text);
  public function message($text);
  public function addAttachment($file);
  public function send();
}
