<?php

/**
 * Email\SES
 *
 * Email\SES SMTP driver.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Email;

class Ses extends Smtp {

  public function __construct($options = null) {
    $options  = (object)$options;
    $region   = isset($options->region) ? $options->region : 'eu-west-1';
    if (empty($options->username) || empty($options->password))
       throw new \Exception("[core.email.ses] You must provide an Amazon SES SMTP username and password", 1);

    Smtp::__construct([
      'host'     => "email-smtp.{$region}.amazonaws.com",
      'secure'   => true,
      'port'     => 465,
      'username' => $options->username,
      'password' => $options->password,
    ]);

    if (!empty($options->from)) $this->from($options->from);
  }

  public function send(Envelope $envelope){
    if (!$envelope->from())
       throw new \Exception("[core.email.ses] Amazon SES needs a registered `from` address", 1);
    return Smtp::send($envelope);
  }

}

