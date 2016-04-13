<?php

/**
 * Deferred
 *
 * Run callback when script execution is stopped.
 *
 * @package core
 * @author gabriele.diener@caffeina.it
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class Deferred {

	protected 	$callback,
				$args;

	public function __construct( callable $callback ) {
		$this->args = array_slice( func_get_args(), 1 );
		$this->callback = $callback;
	}

	public function setArgs() {
		$this->args = func_get_args();
	}

	public function __destruct() {
		call_user_func( $this->callback, $this->args );
	}
}