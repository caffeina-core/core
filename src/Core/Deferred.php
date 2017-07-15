<?php

/**
 * Deferred
 *
 * Run callback when script execution is stopped.
 *
 * @package core
 * @author gabriele.diener@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core;

class Deferred {

	protected $callback,
            $enabled = true;

	final public function __construct( callable $callback ) {
		$this->callback = $callback;
	}

  final public function disarm()  {
    $this->enabled = false;
  }

  final public function prime()  {
    $this->enabled = true;
  }

	final public function __destruct() {
		if ( $this->enabled ) call_user_func( $this->callback );
	}

}