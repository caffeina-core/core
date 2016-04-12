<?php

/**
 * Email\Proxy
 *
 * Simple proxy driver. It pass all emails to an event
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Email;

class Proxy implements Driver {

  protected $listener = 'core.email.proxy.send';

  public function onInit($options){
    if (!empty($options['hook'])) $this->listener = $options['hook'];
  }

  public function onSend(Envelope $envelope){
    \Event::trigger($this->listener, $envelope);
    return true;
  }

}

