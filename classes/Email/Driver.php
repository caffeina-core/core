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

namespace Email;

interface Driver {
  public function send(Envelope $envelope);
}
