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

  public function send(Envelope $envelope){
    // PHP requires direct handling of To and Subject Headers.
    $success     = true;
    $recipients  = $envelope->to();
    $subject     = $envelope->subject();
    $envelope->to(false);
    $envelope->subject(false);
    foreach ($recipients as $to) {
      $current_success = mail($to,$subject,$envelope->body(),$envelope->head());
      \Event::trigger('core.email.send',$to,$envelope,'native');
      $success = $success && $current_success;
    }
    return $success;
  }

}

