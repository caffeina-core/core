<?php

class TokenTest extends PHPUnit_Framework_TestCase {

    public function testEncode(){
				$results = Token::encode("TEST","1234");
        $this->assertEquals("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE", $results);
    }

    public function testDecode(){

				try {
					$results = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE","1234");
				} catch(Exception $e) {
					$this->fail("Exception throwed");
				}

        $this->assertEquals("TEST", $results);
    }

    public function testWrongSecret(){

				try {
					$results = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE","41231");
					$this->fail("Expected exception 'WrongSecret' not thrown");
				} catch(Exception $e) {
					$results = false;
				}

        $this->assertFalse($results);
    }

    public function testInvalidToken(){

				try {
					$results = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi","1234");
					$this->fail("Expected exception 'InvalidToken' not thrown");
				} catch(Exception $e) {
					$results = false;
				}

        $this->assertFalse($results);
    }

}

