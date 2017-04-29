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

	public function __construct( callable $callback ) {
		$this->callback = $callback;
	}

  public function disarm() {
    $this->enabled = false;
  }

  public function prime() {
    $this->enabled = true;
  }

	public function __destruct() {
		if ( $this->enabled ) call_user_func( $this->callback );
	}

}