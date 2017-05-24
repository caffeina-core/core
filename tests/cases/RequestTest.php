<?php

use Core\{
  Request
};

class RequestTest extends PHPUnit_Framework_TestCase {

  function request_set_clear_data() {
    $_GET = [];
    $_POST = [];
    $_COOKIE = [];
    $_FILES = [];
    $_REQUEST = [];
    $_ENV = [];
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

  public function testHOST() {
    $_SERVER['HTTP_X_FORWARDED_HOST'] = 'A,B,C';
    $this->assertEquals('C', Request::host());

    $_SERVER['HOSTNAME'] = 'test.dev';
    $this->assertEquals('C', Request::host());

    unset($_SERVER['HTTP_X_FORWARDED_HOST']);
    $this->assertEquals('test.dev', Request::host());

    $_SERVER['HOSTNAME'] = 'test.dev:80';
    $this->assertEquals('test.dev', Request::host());

    $_SERVER['HOSTNAME'] = 'test.dev:443';
    $this->assertEquals('test.dev', Request::host());

    $_SERVER['HOSTNAME'] = 'test.dev:8080';
    $this->assertEquals('test.dev:8080', Request::host());
  }

  public function testIP() {
    unset($_SERVER['HTTP_X_FORWARDED_HOST']);

    $_SERVER['HTTP_X_FORWARDED_FOR'] = '0.0.0.0,1.2.3.4';
    $this->assertEquals('1.2.3.4', Request::IP());

    $_SERVER['REMOTE_ADDR'] = '10.20.30.40';
    $this->assertEquals('1.2.3.4', Request::IP());

    unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    $this->assertEquals('10.20.30.40', Request::IP());
  }

  public function testURL() {
    $_SERVER['HTTP_HOST'] = $host = 'localhost';
    $_SERVER['HTTPS'] = 'off';
    $this->assertEquals("http://$host/", Request::URL());

    $_SERVER['HTTPS'] = 1;
    $this->assertEquals("https://$host/", Request::URL());

    unset($_SERVER['HTTPS']);
    $this->assertEquals("http://$host/", Request::URL());
  }

  public function testURI() {
    $this->assertEquals(Request::URI(), '/');
  }

  public function testNegotiation() {
    $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
    $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate, sdch=0.5';
    $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en;q=0.6,it,en-US;q=0.8';

    $this->assertEquals('en', Request::accept('language', 'en,es'));
    $this->assertEquals('it', Request::accept('language', 'en,it;q=0.1'));
    $this->assertEquals('gzip', Request::accept('encoding', 'deflate;q=0.6,gzip;q=0.1'));
    $this->assertEquals('application/xhtml+xml', Request::accept('type', 'application/xml,application/xhtml+xml'));
    $this->assertFalse(Request::accept('type', 'svg'));

  }

}
