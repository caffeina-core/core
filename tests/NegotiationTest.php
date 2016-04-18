<?php

class NegotiationTest extends PHPUnit_Framework_TestCase {

	public function testBestMatch() {
		$accept = 'image/*;q=0.9,*/*;q=0.2';
		$choices = 'text/html,svg/xml,image/svg+xml';
		$this->assertEquals(Negotiation::bestMatch($accept, $choices), 'image/svg+xml');
	}

}
