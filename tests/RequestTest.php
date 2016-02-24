<?php

class RequestTest extends PHPUnit_Framework_TestCase {

  function request_set_clear_data(){
    $_GET       = [];
    $_POST      = [];
    $_COOKIE    = [];
    $_FILES     = [];
    $_REQUEST   = [];
    $_ENV       = [];
  }

  function request_set_get_data($data){
    $_GET = $data;
  }

  function request_set_post_data($data){
    $_POST = $data;
  }

  function request_set_cookie_data($data){
    $_COOKIE = $data;
  }

  function request_set_files_data($data){
    $_FILES = $data;
  }

  function request_set_env_data($data){
    $_ENV = $data;
  }

  public function testGet(){
    $this->request_set_get_data(['alpha'=>'beta']);
    $this->assertEquals(Request::get()['alpha'],'beta');
  }

  public function testPost(){
    $this->request_set_post_data(['alpha'=>'beta']);
    $this->assertEquals(Request::post()['alpha'],'beta');
  }

  public function testCookie(){
    $this->request_set_cookie_data(['alpha'=>'beta']);
    $this->assertEquals(Request::cookie()['alpha'],'beta');
  }

  public function testFiles(){
    $this->request_set_files_data(['alpha'=>'beta']);
    $this->assertEquals(Request::files()['alpha'],'beta');
  }

  public function testEnv(){
    $this->request_set_env_data(['alpha'=>'beta']);
    $this->assertEquals(Request::env()['alpha'],'beta');
  }

  public function testURL(){
    $this->assertEquals(Request::URL(),'http:///');
  }

  public function testURI(){
    $this->assertEquals(Request::URI(),'/');
  }

}

