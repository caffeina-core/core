<?php

class TestDict extends Dictionary {
      protected static $fields = null;
}

class DictionaryTest extends PHPUnit_Framework_TestCase {

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
	}

	public function testInit() {
		$this->assertEquals(json_encode(TestDict::all()), "[]");
	}

	public function testLoad() {
		TestDict::load($this->data);
		$this->assertEquals(json_encode(TestDict::all()), json_encode($this->data));
	}

	public function testClear() {
		TestDict::clear();
		$this->assertEquals(json_encode(TestDict::all()), "[]");
	}

	public function testSetGet() {
		TestDict::set('a', 999);
		$this->assertEquals(TestDict::all()['a'], 999);
		$this->assertEquals(TestDict::get('a'), 999);
	}

	public function testExists() {
		TestDict::set('a', 999);
		$this->assertTrue(TestDict::exists('a'));
		$this->assertFalse(TestDict::exists('not-a'));
	}

	public function testLeftMerge() {
		TestDict::merge($this->data, true);
		$this->assertEquals(TestDict::all()['a'], 999);
	}

	public function testRightMerge() {
		TestDict::clear();
		TestDict::set('a', 999);
		TestDict::merge($this->data);
		$this->assertEquals(TestDict::all()['a'], 123);
	}

	public function testDefaultOnGetFail() {
		$this->assertEquals(TestDict::get('i-dont-exists', 'OK'), 'OK');
		$this->assertEquals(TestDict::get('i-dont-exists', function () {return 'OK';}), 'OK');
	}

	public function testSetGetFromPath() {
		TestDict::clear();
		$this->assertEquals(TestDict::set('a.b.c.d', 1), 1);
		$this->assertEquals(TestDict::get('a.b.c.d', 0), 1);
	}

  public function testSetFromObjectMap() {
    TestDict::clear();
    TestDict::load([
       'a' => [
         'a' => 'AA',
         'b' => 'AB',
       ],
       'b' => 'B',
    ]);

    $res = TestDict::get([
      'a.a' => 'X',
      'b'   => 'Y',
      'a.b' => 'Z',
      'a'   => 'W',
    ]);

    $this->assertEquals('{"X":"AA","Y":"B","Z":"AB","W":{"a":"AA","b":"AB"}}', json_encode($res));
  }

}
