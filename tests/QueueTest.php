<?php

class QueueTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
	}

	public function testDatabaseQueue() {
    Queue::using('database');
		$this->assertEquals(1,1);
	}

}
