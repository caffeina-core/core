<?php

class HashTest extends PHPUnit_Framework_TestCase {

	public function testAlgos() {
		$this->assertNotEmpty($algos = Hash::methods());
		$testAlgo = $algos[0];
		$this->assertTrue(Hash::can($testAlgo));

		$payload = [1, 2, 3];
		$hash = Hash::make($payload, $testAlgo);
		$this->assertNotEmpty($hash);
		$this->assertTrue(Hash::verify($payload, $hash, $testAlgo));
	}

	public function testUUID() {
		$this->assertNotEquals(Hash::uuid(), Hash::uuid());
		$namespace = Hash::uuid();

		$this->assertFalse(Hash::uuid(3, 'not-valid!', '123'));
		$this->assertEquals(Hash::uuid(3, $namespace, '123'), Hash::uuid(3, $namespace, '123'));
		$this->assertNotEquals(Hash::uuid(3, $namespace, '123'), Hash::uuid(3, $namespace, '321'));
		$this->assertNotEquals(Hash::uuid(3, Hash::uuid(), '123'), Hash::uuid(3, $namespace, '123'));

		$this->assertFalse(Hash::uuid(5, 'not-valid!', '123'));
		$this->assertEquals(Hash::uuid(5, $namespace, '123'), Hash::uuid(5, $namespace, '123'));
		$this->assertNotEquals(Hash::uuid(5, $namespace, '123'), Hash::uuid(5, $namespace, '321'));
		$this->assertNotEquals(Hash::uuid(5, Hash::uuid(), '123'), Hash::uuid(5, $namespace, '123'));
	}

	public function testMurmur() {
    $this->assertTrue(Hash::can("murmur"));

		$this->assertNotEquals(Hash::murmur("Hello World", 0), Hash::uuid("Hello World", 1));

		$this->assertEquals("cnd0ue", Hash::murmur("Hello World", 0));

    $this->assertEquals(427197390, Hash::murmur("Hello World", 0, true));

    $this->assertNotEmpty(Hash::random());
    $this->assertNotEquals(Hash::random(),Hash::random());
	}

}
