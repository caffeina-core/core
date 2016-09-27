<?php

class RequestTest extends PHPUnit_Framework_TestCase {

	function request_set_clear_data() {
		$_GET     = [];
		$_POST    = [];
		$_COOKIE  = [];
		$_FILES   = [];
		$_REQUEST = [];
		$_ENV     = [];
	}

	function request_set_get_data($data) {
		$_GET = $data;
	}

	function request_set_post_data($data) {
		$_POST = $data;
	}

	function request_set_cookie_data($data) {
		$_COOKIE = $data;
	}

	function request_set_files_data($data) {
		$_FILES = $data;
	}

	function request_set_env_data($data) {
		$_ENV = $data;
	}

	public function testGet() {
		$this->request_set_get_data(['alpha' => 'beta']);
		$this->assertEquals('beta', Request::get()['alpha']);
	}

	public function testPost() {
		$this->request_set_post_data(['alpha' => 'beta']);
		$this->assertEquals('beta', Request::post()['alpha']);
	}

	public function testCookie() {
		$this->request_set_cookie_data(['alpha' => 'beta']);
		$this->assertEquals('beta', Request::cookie()['alpha']);
	}

	public function testFiles() {
		$this->request_set_files_data(['alpha' => 'beta']);
		$this->assertEquals('beta', Request::files()['alpha']);
	}

	public function testEnv() {
		$this->request_set_env_data(['alpha' => 'beta']);
		$this->assertEquals('beta', Request::env()['alpha']);
	}

	public function testURL() {
		$this->assertEquals('http://localhost/', Request::URL());
	}

	public function testURI() {
		$this->assertEquals(Request::URI(), '/');
	}

	public function testNegotiation() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate, sdch=0.5';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en;q=0.6,it,en-US;q=0.8';
		// $_SERVER['HTTP_CHARSET']      = '';

		$this->assertEquals('en',Request::accept('language', 'en,es'));
		$this->assertEquals('it',Request::accept('language', 'en,it;q=0.1'));
		$this->assertFalse(Request::accept('type', 'svg'));
		$this->assertEquals('gzip',Request::accept('encoding', 'deflate;q=0.6,gzip;q=0.1'));
		$this->assertEquals('utf-8',Request::accept('charset', 'utf-8,*;q=0.1'));
		$this->assertEquals('application/xhtml+xml',Request::accept('type', 'application/xml,application/xhtml+xml'));

	}

}
