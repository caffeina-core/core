<?php

use Core\{
  Filter
};

class FilterTest extends PHPUnit_Framework_TestCase {

	public function testAdd() {
		Filter::add('test', function ($text) {
			return strtoupper($text);
		});
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('ALPHA', $results);
	}

	public function testAddMultiple() {
		Filter::add(['test','bbb'], function ($text) {
			return '_' . $text . '_';
		});
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('_ALPHA_', $results);
    $results = Filter::with('bbb', 'alpha');
    $this->assertEquals('_alpha_', $results);
	}

	public function testAddArray() {
		Filter::add([
			'test' => function ($text) {
				return strtolower($text);
			},
			'test1' => function ($text) {
				return strtoupper($text);
			},
			'test2' => function ($text) {
				return strtoupper($text);
			}
		]);
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('_alpha_', $results);
		$results = Filter::with('test1', 'alpha');
		$this->assertEquals('ALPHA', $results);
		$results = Filter::with('test2', 'alpha');
		$this->assertEquals('ALPHA', $results);
	}

	public function testRemove() {
		Filter::remove('test');
		$results = Filter::with('test', 'alpha');
		$this->assertEquals('alpha', $results);
	}

}
