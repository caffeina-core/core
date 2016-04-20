<?php

class OptionsTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		$this->data = [
			'a' => 123,
			'b' => 'hello',
			'c' => [
				'r' => '#f00',
				'g' => '#0f0',
				'b' => '#00f',
			],
		];

    Options::loadArray($this->data, 'test');
	}

  public function testGet() {
    $this->assertEquals(json_encode(Options::get('test')), json_encode($this->data));
  }

  public function testGetFromPath() {
    $this->assertEquals(Options::get('test.c.r'), '#f00');
  }

  public function testGetDefault() {
    $this->assertEquals(Options::get('testificate','xxx'), 'xxx');
    $this->assertEquals(Options::get('testificate'), 'xxx');
  }

  public function testGetDefaultFromPath() {
    $this->assertEquals('567',Options::get('test.d', '567'));
    $this->assertEquals('567',Options::get('test.d'));
  }

}
