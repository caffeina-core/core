<?php

/**
 * Email\SES
 *
 * Email\SES SMTP driver.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace Email;

class Ses extends Smtp {

  public function __construct($options = null) {
    $options  = (object)$options;
    $region   = isset($options->region) ? $options->region : 'eu-west-1';
    if (empty($options->username) || empty($options->password))
       throw new \Exception("[Core.Email.SES] You must provide Amazon SES SMTP username and password", 1);

    Smtp::__construct([
      'host'     => "email-smtp.{$region}.amazonaws.com",
      'secure'   => true,
      'port'     => 465,
      'username' => $options->username,
      'password' => $options->password,
    ]);

    if (!empty($options->from)) $this->from($options->from);
  }

  public function send(){
    if (empty($this->from->full))
       throw new \Exception("[Core.Email.SES] Amazon SES needs a registered `from` address", 1);
    return Smtp::send();
  }

}

