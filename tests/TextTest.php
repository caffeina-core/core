<?php

class TextTest extends PHPUnit_Framework_TestCase {

	public function testRenderWithoutData() {
		$results = Text::render("TEST:{{ a}}:{{b}}:{{c}}");
		$this->assertEquals("TEST:::", $results);
	}

  public function testMalformedRender() {
    $this->assertEquals("TEST:{{c}:SETS", Text::render("TEST:{{c}:SETS",["c"=>1]));
  }

	public function testRenderWithData() {
		$this->assertEquals("TEST:1:2:", Text::render("TEST:{{ a}}:{{ b   }}:{{c}}", [
			'a' => 1,
			'b' => 2,
		]));
	}

	public function testRenderWithDeepData() {
		$this->assertEquals("TEST:1:2:", Text::render("TEST:{{a.x.y}}:{{b.x}}:{{a.b.x.y.u}}", [
			'a' => [
				'x' => [
					'y' => 1,
				],
			],
			'b' => [
				'x' => 2,
			],
		]));
	}

  public function testRemoveAccents() {
    $results = Text::removeAccents("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ");
    $this->assertEquals("aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY", $results);
  }

  public function testSlugify() {
    $results = Text::slugify("Thîs îs --- à vêry wrong séntènce!");
    $this->assertEquals("this-is-a-very-wrong-sentence", $results);
  }

  public function testCut() {
    $txt = "Name: Ethan Hunt; Role: Agent";

    $results = Text::cut($txt,'Name: ',';');
    $this->assertEquals("Ethan Hunt", $results);

    $results = Text::cut($txt,'Name: ');
    $this->assertEquals("Ethan Hunt; Role: Agent", $results);
  }

}
