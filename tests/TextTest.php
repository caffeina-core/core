<?php

class TextTest extends PHPUnit_Framework_TestCase {

    public function testRenderWithoutData(){
      $results = Text::render("TEST:{{ a}}:{{b}}:{{c}}");
      $this->assertEquals("TEST:::", $results);
    }


    public function testRenderWithData(){
      $this->assertEquals("TEST:1:2:", Text::render("TEST:{{ a}}:{{ b   }}:{{c}}",[
        'a' => 1,
        'b' => 2,
      ]));
    }

    public function testRenderWithDeepData(){
      $this->assertEquals("TEST:1:2:", Text::render("TEST:{{a.x.y}}:{{b.x}}:{{a.b.x.y.u}}",[
        'a' => [
          'x' => [
            'y' => 1
          ]
        ],
        'b' => [
          'x' => 2
        ],
      ]));
    }


}

