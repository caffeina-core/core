<?php

use Core\{
  Event
};

class EventTest extends PHPUnit_Framework_TestCase {

	public function testSimple() {
		$value = 0;

		Event::on('test.event.alpha', function ($override = null) use (&$value) {
			$value = $override ?: 'alpha';
		});

		Event::trigger('test.event.alpha');
		$this->assertEquals('alpha', $value);

		Event::trigger('test.event.alpha', 1234);
		$this->assertEquals(1234, $value);

		Event::on('test.event.alpha', function () use (&$value) {
			$value .= '2';
		});

		Event::trigger('test.event.alpha');
		$this->assertEquals('alpha2', $value);

		/*********************************************************/
		$value = 123;
		Event::off('test.event.alpha');
		Event::trigger('test.event.alpha');
		//test($value == 123,'Event','Remove all triggers.');
		$this->assertEquals(123, $value);

		/*********************************************************/
		$value = 1;
		Event::on('test.event', function () use (&$value) {
			$value *= 2;
		});

		Event::triggerOnce('test.event');
		Event::triggerOnce('test.event');
		//test($value == 2,'Event','Trigger Once.');
		$this->assertEquals(2, $value);

		/*********************************************************/
		$value = 0;

		Event::single('test.event', function () use (&$value) {
			$value += 1;
		});

		// This must override the +=1
		Event::single('test.event', function () use (&$value) {
			$value += 2;
		});

		Event::trigger('test.event');
		//test($value == 2,'Event','Single handler.');
		$this->assertEquals(2, $value);

		/*********************************************************/
		$value = 0;
		Event::off('test.event');

		Event::on('test.event', function () use (&$value) {
			$value += 1;
		});

		Event::alias('test.event', 'the.dude');

		Event::trigger('test.event');
		Event::trigger('the.dude');
		//test($value == 2,'Event','Alias.');
		$this->assertEquals(2, $value);

	}

}
