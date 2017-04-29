<?php

use Core\Cache;

class CacheTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		Cache::using('memory');
	}

	public function testSetGet() {
		Cache::set('test', 'ALPHA');
		$this->assertEquals('ALPHA', Cache::get('test'));
	}

	public function testGetUnknown() {
		$this->assertEquals('BETA', Cache::get('test2', 'BETA'));
		$this->assertEquals('BETA', Cache::get('test2'));
	}

	public function testGetDefaultFromClosure() {
		$this->assertEquals('SLOW_DATA :)', Cache::get('test3', function () {
			return "SLOW_DATA :)";
		}));
	}

}
