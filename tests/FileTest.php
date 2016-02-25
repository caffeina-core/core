<?php

class FileTest extends PHPUnit_Framework_TestCase {

    public function __construct(){
      File::mount('temp','native',[
        'root' => sys_get_temp_dir(),
      ]);

      File::mount('mem','memory');
    }

    public function testMounts(){
      $this->assertEquals('["temp","mem"]',json_encode(File::mounts()));
    }

    public function testResolvePath(){
      File::write('mem://my/cool/data2.txt','OK');
      $this->assertEquals('OK',File::read('mem://my/./cool/foo/../data2.txt'));
    }

    public function testSearch(){
      File::write('temp://core-test.txt','TESTIFICATE');
      $this->assertEquals('TESTIFICATE',File::read('core-test.txt'));

      $this->assertTrue(array_search("temp://core-test.txt", File::search("*.txt"))!==false);
    }

    public function testReadWrite(){
      File::write('mem://my/file.txt','Hello World!');

      $this->assertTrue(File::exists('mem://my/file.txt'));

      $this->assertEquals('Hello World!',File::read('mem://my/file.txt'));

      File::write('mem://my/file.txt','Second Test');
      $this->assertEquals('Second Test',File::read('mem://my/file.txt'));
    }

    public function testAppends(){
      File::append('mem://my/cool/data.txt','1');
      File::append('mem://my/cool/data.txt','2');
      File::append('mem://my/cool/data.txt','3');

      $this->assertEquals('123',File::read('mem://my/cool/data.txt'));
    }

}




