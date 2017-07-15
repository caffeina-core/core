<?php

use Core\{
  Structure
};

class StructureTest extends PHPUnit_Framework_TestCase {

	public function __construct() {

		$this->array = [
			'a' => 123,
			'b' => 'hello',
			'c' => [
				'r' => '#f00',
				'g' => '#0f0',
				'b' => '#00f',
			],
		];

		$this->object = (object) [
			'a' => 123,
			'b' => 'hello',
			'c' => (object) [
				'r' => '#f00',
				'g' => '#0f0',
				'b' => '#00f',
			],
		];

	}

	public function testAccess() {
		$test = new Structure($this->array, false);
		$this->assertEquals($test['a'], 123); // Access as Array from Array
		$this->assertEquals($test->b, 'hello'); // Access as Structure from Array
	}

	public function testDeepArray() {
		$test = new Structure($this->array);

		$this->assertEquals($test->c['r'], '#f00'); // Deep access as Structure from Array
		$this->assertEquals($test['c']->g, '#0f0'); // Deep access as Array from Array
		$this->assertEquals($test['c']['b'], '#00f'); // Deep access as DoubleArray from Array
		$this->assertEquals($test->c->b, '#00f'); // Deep access as DoubleStructure from Array
	}

	public function testDeepStructure() {
		$test = new Structure($this->object);

		$this->assertEquals($test->c['r'], '#f00'); // Deep access as Structure from Structure
		$this->assertEquals($test['c']->g, '#0f0'); // Deep access as Array from Structure
		$this->assertEquals($test['c']['b'], '#00f'); // Deep access as DoubleArray from Structure
		$this->assertEquals($test->c->b, '#00f'); // Deep access as DoubleStructure from Structure
	}

	public function testFetch() {
		$data = [
			'a' => [
				'x' => [
					'y' => 1,
				],
			],
			'b' => [
				'x' => 2,
			],
		];
		$this->assertEquals(1, Structure::fetch('a.x.y', $data), "Deep fetch");

		$this->assertEquals('{"y":1}', json_encode(Structure::fetch('a.x', $data), JSON_NUMERIC_CHECK));
		$this->assertEquals('{"y":1}', json_encode(Structure::fetch('a.x', $data), JSON_NUMERIC_CHECK), "Protect \$data reference");
	}

}
