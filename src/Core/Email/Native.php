<?php

/**
 * Email\Native
 *
 * Email\Native PHP mail() driver.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core\Email;

class Native implements Driver {

  public function onInit($options){}

  public function onSend(Envelope $envelope){
    $results 		= [];
    $recipients 	= $envelope->to();
    $envelope->to(false);
    foreach ($recipients as $to) {
      $results[$to] = mail($to,$envelope->subject(),$envelope->body(),$envelope->head());
    }
    return $results;
  }

}

