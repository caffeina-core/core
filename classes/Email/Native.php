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

namespace Email;

class Native implements Driver {
  
  public function onInit($options){}

  public function onSend(Envelope $envelope){
    $results = [];
    foreach ($envelope->to() as $to) {
      $results[$to] = mail($to,$envelope->subject(),$envelope->body(),$envelope->head());
    }
    return $results;
  }

}

