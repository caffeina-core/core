<?php

use Core\{
  Options
};

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

	public function testLoadENV() {
		$tempfile = tempnam('/tmp', 'core-test-env');
		file_put_contents($tempfile, <<<'EOENV'

  # This is a comment
BASE_DIR="/var/webroot/project-root"


#
#=================#
#

CACHE_DIR="${BASE_DIR}/cache"

export      TMP_DIR="${BASE_DIR}/tmp"

EOENV
		);


		$test_loaded_event = false;
		Options::on('loaded', function () use (&$test_loaded_event) {
			$test_loaded_event = true;
		});

		Options::loadENV(dirname($tempfile), basename($tempfile), 'ENV');

		$this->assertTrue($test_loaded_event, "Loaded Event");
		Options::off('loaded');

		$this->assertEquals(3, count((array) Options::get('ENV')), "ENV count");

		$this->assertEquals("/var/webroot/project-root/tmp", Options::get('ENV.TMP_DIR'), "ENV.TMP_DIR");
		$this->assertEquals("/var/webroot/project-root/cache", Options::get('ENV.CACHE_DIR'), "ENV.CACHE_DIR");
	  $this->assertEquals("/var/webroot/project-root", Options::get('ENV.BASE_DIR'), "ENV.BASE_DIR");
	}

	public function testGet() {
		$this->assertEquals(json_encode(Options::get('test')), json_encode($this->data));
	}

	public function testGetFromPath() {
		$this->assertEquals(Options::get('test.c.r'), '#f00');
	}

	public function testGetDefault() {
		$this->assertEquals(Options::get('testificate', 'xxx'), 'xxx');
		$this->assertEquals(Options::get('testificate'), 'xxx');
	}

	public function testGetDefaultFromPath() {
		$this->assertEquals('567', Options::get('test.d', '567'));
		$this->assertEquals('567', Options::get('test.d'));
	}

	public function testEmptyOverride() {
		Options::clear();
		Options::loadArray([
			"base" => true,
			"elements" => [1, 2, 3, 4],
			"tree" => [
				"type" => "SIMPLE",
				"child" => [
					"A", "B", "C",
				],
			],
		]);
		Options::loadArray([
			"base" => false,
			"elements" => null, // But don't use [] ! see array_replace_recursive for an explanation.
		]);
		$this->assertEquals(0, count(Options::get('elements', [])));
	}

}
