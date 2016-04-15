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

class Deferred {

	protected $callback;

	public function __construct( callable $callback ) {
		$this->callback = $callback;
	}

	public function __destruct() {
		call_user_func( $this->callback );
	}

}