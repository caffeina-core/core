<?php

class FilterTest extends PHPUnit_Framework_TestCase {

	public function testAdd() {
		Filter::add('test', function ($text) {
			return strtoupper($text);
		});
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('ALPHA', $results);
	}

	public function testAddMultiple() {
		Filter::add('test', function ($text) {
			return '_' . $text . '_';
		});
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('_ALPHA_', $results);
	}

	public function testRemove() {
		Filter::remove('test');
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('alpha', $results);
	}

}
