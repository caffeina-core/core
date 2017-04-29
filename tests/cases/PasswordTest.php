<?php

use Core\{
  Password
};

class PasswordTest extends PHPUnit_Framework_TestCase {

	public function testMakeAndVerify() {
		$the_password = "MyLittleSecret";

		$hash = Password::make($the_password);
		$hash_2 = Password::make("Fake!");

		$this->assertNotEmpty($hash);
		$this->assertNotEmpty($hash_2);

		$this->assertTrue(Password::verify($the_password, $hash));
		$this->assertFalse(Password::verify($the_password, $hash_2));

		$this->assertTrue(Password::compare($the_password, $the_password));
		$this->assertFalse(Password::compare($the_password, "not-the-same"));

	}

}
