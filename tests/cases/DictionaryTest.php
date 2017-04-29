<?php

use Core\Dictionary;


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
      'this' => [ 'must' => [ 'be' => false ] ]
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
		$this->assertEquals(999,TestDict::all()['a'],"From all()");
		$this->assertEquals(999,TestDict::get('a'), "From getter");
	}

	public function testExists() {
		TestDict::set('a', 999);
		$this->assertTrue(TestDict::exists('a'),"Must Exists");
		$this->assertFalse(TestDict::exists('not-a'),"Must NOT Exists");
	}

	public function testLeftMerge() {
		TestDict::merge($this->data, true);
		$this->assertEquals(999,TestDict::all()['a']);
	}

	public function testRightMerge() {
		TestDict::clear();
		TestDict::set('a', 999);
		TestDict::merge($this->data);
		$this->assertEquals($this->data['a'], TestDict::all()['a']);
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

  public function testGetFalsy() {
    TestDict::clear();
    TestDict::load($this->data);
    $this->assertTrue(TestDict::all()['this']['must']['be'] === TestDict::get('this.must.be'));
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
      'X' => 'a.a',
      'Y' => 'b',
      'Z' => 'a.b',
      'W' => 'a',
    ]);

    $this->assertEquals('{"X":"AA","Y":"B","Z":"AB","W":{"a":"AA","b":"AB"}}', json_encode($res));
  }

}
