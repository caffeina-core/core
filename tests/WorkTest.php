<?php

class WorkTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
	
	}

	public function test() {
		Work::add(function(){
			for ($i = 0; $i <= 10; $i++) {
				echo "Value: $i \n";
				yield;
			}
		});

		Work::add(function(){
			for ($i = 0; $i <= 10; $i++) {
				echo "Value: $i \n";
				yield;
			}
		});


		Work::run();
	}

}
