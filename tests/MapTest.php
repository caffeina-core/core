<?php

class MapTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->data = [
			'a' => 123,
			'b' => 'hello',
			'c' => [
				'r' => '#f00',
				'g' => '#0f0',
				'b' => '#00f',
			],
		];

    $this->map = new Map();
	}

	public function testInit() {
		$this->assertEquals(json_encode($this->map->all()), "[]");
	}

	public function testLoad() {
		$this->map->load($this->data);
		$this->assertEquals(json_encode($this->map->all()), json_encode($this->data));
	}

	public function testClear() {
		$this->map->clear();
		$this->assertEquals(json_encode($this->map->all()), "[]");
	}

	public function testSetGet() {
		$this->map->set('a', 999);
		$this->assertEquals($this->map->all()['a'], 999);
		$this->assertEquals($this->map->get('a'), 999);
	}

	public function testExists() {
		$this->map->set('a', 999);
		$this->assertTrue($this->map->exists('a'));
		$this->assertFalse($this->map->exists('not-a'));
	}

	public function testLeftMerge() {
    $this->map->set('a', 999);
		$this->map->merge($this->data, true);
		$this->assertEquals($this->map->all()['a'], 999);
	}

	public function testRightMerge() {
		$this->map->set('a', 999);
		$this->map->merge($this->data);
		$this->assertEquals($this->map->all()['a'], 123);
	}

	public function testDefaultOnGetFail() {
		$this->assertEquals($this->map->get('i-dont-exists', 'OK'), 'OK');
		$this->assertEquals($this->map->get('i-dont-exists', function () {return 'OK';}), 'OK');
	}

	public function testSetGetFromPath() {
		$this->map->clear();
		$this->assertEquals($this->map->set('a.b.c.d', 1), 1);
		$this->assertEquals($this->map->get('a.b.c.d', 0), 1);
	}

}
