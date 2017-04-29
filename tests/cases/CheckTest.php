<?php

use Core\Check;

class CheckTest extends PHPUnit_Framework_TestCase {

	public function testRequired() {
		$this->assertTrue(Check::valid([
			'value' => 'required',
		], ['value' => '1']));
		$this->assertFalse(Check::valid([
			'value' => 'required',
		], ['value' => '']));
		$this->assertTrue(Check::valid([
			'value' => 'required',
		], ['value' => 0]));
	}

	public function testMultipleNumeric() {
		$this->assertTrue(Check::valid([
			'value' => 'required|numeric',
		], ['value' => '123']));
		$this->assertFalse(Check::valid([
			'value' => 'required|numeric',
		], ['value' => 'aaa']));
	}

	public function testAlphanumeric() {
		$this->assertTrue(Check::valid([
			'value' => 'alphanumeric',
		], ['value' => 'A123']));
		$this->assertFalse(Check::valid([
			'value' => 'alphanumeric',
		], ['value' => 'A #']));
	}

	public function testTrue() {
		$this->assertTrue(Check::valid([
			'value' => 'true',
		], ['value' => 1]));
		$this->assertFalse(Check::valid([
			'value' => 'true',
		], ['value' => '']));
	}

	public function testFalse() {
		$this->assertTrue(Check::valid([
			'value' => 'false',
		], ['value' => false]));
		$this->assertFalse(Check::valid([
			'value' => 'false',
		], ['value' => '123']));
	}

	public function testEmail() {
		$this->assertTrue(Check::valid([
			'value' => 'email',
		], ['value' => 'test@alpha.com']));
		$this->assertFalse(Check::valid([
			'value' => 'email',
		], ['value' => 'test@a.c@co']));
	}

	public function testURL() {
		$this->assertTrue(Check::valid([
			'value' => 'url',
		], ['value' => 'https://adsasd.example.com/test?aaa=123&ves=123']));
		$this->assertFalse(Check::valid([
			'value' => 'url',
		], ['value' => 'adsasd.example.com//test??asd']));
	}

	public function testMax() {
		$this->assertTrue(Check::valid([
			'value' => 'max:50',
		], ['value' => 12]));
		$this->assertFalse(Check::valid([
			'value' => 'max:50',
		], ['value' => 123]));
	}

	public function testMin() {
		$this->assertTrue(Check::valid([
			'value' => 'min:10',
		], ['value' => 15]));
		$this->assertFalse(Check::valid([
			'value' => 'min:50',
		], ['value' => 15]));
	}

	public function testRange() {
		$this->assertTrue(Check::valid([
			'value' => 'range:10,50',
		], ['value' => 12]));
		$this->assertFalse(Check::valid([
			'value' => 'range:10,50',
		], ['value' => 4]));
		$this->assertTrue(Check::valid([
			'value' => 'range:-30,-5',
		], ['value' => -12]));
	}

	public function testInArray() {
		$this->assertTrue(Check::valid([
			'value' => 'in_array:[10,50,"beta"]',
		], ['value' => 10]));
		$this->assertTrue(Check::valid([
			'value' => 'in_array:[10,50,"beta"]',
		], ['value' => 50]));
		$this->assertFalse(Check::valid([
			'value' => 'in_array:[10,50,"beta"]',
		], ['value' => 4]));
		$this->assertTrue(Check::valid([
			'value' => 'in_array:[10,50,"beta"]',
		], ['value' => "beta"]));
	}

	public function testWords() {
		$this->assertTrue(Check::valid([
			'value' => 'words:3',
		], ['value' => "This is Valid"]));
		$this->assertFalse(Check::valid([
			'value' => 'words:3',
		], ['value' => "This is not valid"]));
	}

	public function testLength() {
		$this->assertTrue(Check::valid([
			'value' => 'length:3',
		], ['value' => "123"]));
		$this->assertFalse(Check::valid([
			'value' => 'length:3',
		], ['value' => "1234"]));
	}

	public function testSameAs() {
		$this->assertTrue(Check::valid([
			'value' => 'same_as:"source"',
		], ['source' => '123', 'value' => "123"]));
		$this->assertFalse(Check::valid([
			'value' => 'same_as:"source"',
		], ['source' => '123', 'value' => "1234"]));
	}

	public function testCustomMethod() {
		Check::method('custom', [
			"validate" => function ($value, $a, $b, $c) {
				return $value == "$a$b$c";
			},
			"message" => "This must be '{{arg_1}}{{arg_2}}{{arg_3}}'",
		]);
		$this->assertTrue(Check::valid([
			'value' => 'custom:1,2,3',
		], ['value' => "123"]));
		$this->assertFalse(Check::valid([
			'value' => 'custom:1,3,2',
		], ['value' => "1234"]));
	}

}
