<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		// Build some templates
		$TEMPLATE_DIR = sys_get_temp_dir();
		@mkdir("$TEMPLATE_DIR/special");

		file_put_contents("$TEMPLATE_DIR/special/hello.php", <<<'EOT'
Hello, <?= $this->name ?>!
EOT
		);

		file_put_contents("$TEMPLATE_DIR/test.php", <<<'EOT'
TESTIFICATE
EOT
		);

		file_put_contents("$TEMPLATE_DIR/global.php", <<<'EOT'
<?=$this->THE_DARKNESS?>
EOT
		);

		file_put_contents("$TEMPLATE_DIR/test_var.php", <<<'EOT'
<?=$this->var?>
EOT
		);

    file_put_contents("$TEMPLATE_DIR/article-123.php", <<<'EOT'
ARTICLE-123:<?=$this->var?>
EOT
    );

    file_put_contents("$TEMPLATE_DIR/article.php", <<<'EOT'
ARTICLE:<?=$this->var?>
EOT
    );

		file_put_contents("$TEMPLATE_DIR/index_pass.php", <<<'EOT'
[<?= $this->partial('special/hello') ?>]
EOT
		);

		file_put_contents("$TEMPLATE_DIR/index_override.php", <<<'EOT'
[<?= $this->partial('special/hello',['name'=>'Daryl']) ?>]
EOT
		);

		// Init View handler
		View::using(new View\PHP($TEMPLATE_DIR));
	}

	public function testRender() {
		$results = (string) View::from('test');
		$this->assertEquals('TESTIFICATE', $results);
	}

	public function testRenderWithParameters() {
		$results = (string) View::from('test_var')->with(['var' => 1]);
		$this->assertEquals('1', $results);
	}

	public function testRenderWithParametersShorthand() {
		$results = (string) View::from('test_var')->with(['var' => 1]);
		$this->assertEquals('1', $results);
	}

	public function testGlobalParameters() {
		View::addGlobal('THE_DARKNESS', 'Jakie');
		$results = (string) View::from('global');
		$this->assertEquals('Jakie', $results);
	}

	public function testPHPViewSimpleRenderWithVariables() {
		$results = (string) View::from('special/hello', ['name' => 'Rick']);
		$this->assertEquals('Hello, Rick!', $results);
	}

	public function testPHPViewPartialsRenderWithVariables() {
		$results = (string) View::from('index_pass', ['name' => 'Rick']);
		$this->assertEquals('[Hello, Rick!]', $results);
	}

	public function testPHPViewPartialsOverridingVariables() {
		$results = (string) View::from('index_override', ['name' => 'Rick']);
		$this->assertEquals('[Hello, Daryl!]', $results);
	}

	public function testTemplateExists() {
		$this->assertTrue(View::exists('special/hello'));
		$this->assertFalse(View::exists('im/fake/template'));
	}

  public function testFallbackException() {
    // Test exception on fail
    try {
      $view = View::from([
        'foo',
        'bar',
        'baz',
      ]);
    } catch(Exception $e){
      // Ok, exception catched
      return $this->assertTrue(true,'Exception on view fallback error');
    }
    // Error, an exception must be throwed
    return $this->assertTrue(false,'Exception on view fallback error');
  }

  public function testFallback() {
    $this->assertEquals('ARTICLE-123:626', View::from([
      'undefined',
      'article-123',
      'article',
    ],[
      'var' => 626
    ]));

    $this->assertEquals('ARTICLE:626', View::from([
      'undefined',
      'article-9999',
      'article',
    ],[
      'var' => 626
    ]));

  }

}
