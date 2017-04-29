<?php

/**
 * Email\Driver
 *
 * Email services common interface.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core\Email;

interface Driver {
  public function onInit($options);
  public function onSend(Envelope $envelope);
}
