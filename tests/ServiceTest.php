<?php


class ServiceTest extends PHPUnit_Framework_TestCase {

    public function testSimpleServiceContainer(){
      Service::register('email',function() {
          return "EMAIL SERVICE";
      });
      $this->assertEquals(Service::email() . Service::email(), "EMAIL SERVICEEMAIL SERVICE");
    }

    public function testSimpleServicePersistence(){
      Service::register('test',function($data) {
          return (object)["data" => $data];
      });
      $this->assertEquals(Service::test('--TEST--')->data, "--TEST--");
      $this->assertEquals(Service::test()->data, "--TEST--");
      $this->assertEquals(Service::test("NOT ME!")->data, "--TEST--");
    }

    public function testSimpleServiceFactory(){
      Service::registerFactory('foos',function($bar) {
          return (object)["data" => $bar];
      });
      $this->assertEquals(implode('',[
        Service::foos('A')->data,
        Service::foos('B')->data,
        Service::foos('C')->data,
      ]), "ABC");
    }

}
