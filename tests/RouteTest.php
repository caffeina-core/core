<?php

class RouteTest extends PHPUnit_Framework_TestCase {

  public function __construct(){
    Options::set('core.response.autosend', false);
  }

	private function mock_request($uri, $method) {
		Filter::remove('core.request.method');
		Filter::remove('core.request.URI');
		Filter::add('core.request.URI', function ($x) use ($uri) {return $uri;});
		Filter::add('core.request.method', function ($x) use ($method) {return $method;});
	}

	public function testBasicRouting() {
		Response::clean();
		Route::on('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasGet() {
		Response::clean();
		Route::get('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasPost() {
		Response::clean();
		Route::post('/', function () {
			return "index";
		});
		Route::dispatch('/', 'post');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testRouteNotFound() {
		Response::clean();
		$test = $this;
		Event::on(404, function () use (&$test) {
			$test->assertEquals(404, 404);
		});
		Route::dispatch('/this/is/a/404', 'get');
		Event::off(404);
	}

	public function testWildcardMethod() {
		Route::any('/any', function () {return "ANY";});
		Response::clean();
		Route::dispatch('/any', 'patch');
		$this->assertEquals(Response::body(), 'ANY');
		Response::clean();
		Route::dispatch('/any', 'get');
		$this->assertEquals(Response::body(), 'ANY');
		Response::clean();
		Route::dispatch('/any', 'post');
		$this->assertEquals(Response::body(), 'ANY');
	}

	public function testParameterExtraction() {
		Response::clean();
		Route::on('/post/:a/:b', function ($a, $b) {return "$b-$a";});
		Route::dispatch('/post/1324/fefifo', 'get');
		$this->assertEquals(Response::body(), 'fefifo-1324');
	}

	public function testMiddlewares() {
		Response::clean();
		Route::on('/middle', function () {return "-Test-";})
			->before(function () {echo 'AA';})
			->before(function () {echo 'B';})
			->after(function () {echo 'AA';})
			->after(function () {echo 'B';})
		;
		Route::dispatch('/middle', 'get');
		$this->assertEquals(Response::body(), 'BAA-Test-AAB');
	}

	public function testGroups() {
		Response::clean();
		$this->mock_request('/api1/v1/info', 'get');
		$api = Route::group('/api1', function () {
			Route::on('/info', function () {echo "API-INFO";});
			Route::group('/v1', function () {
				Route::on('/', function () {echo "API-V1";});
				Route::on('/info', function () {echo "API-V1-INFO";});
			});
		});

		Route::dispatch('/api1/v1/info', 'get');
		$this->assertEquals(Response::body(), 'API-V1-INFO');
	}

	public function testGroupsMiddlewares() {
		Response::clean();
		$this->mock_request('/api2/v1/info', 'get');
		Route::group('/api2', function () {
			Route::on('/info', function () {echo "API-INFO";});
			Route::group('/v1', function () {
				Route::on('/', function () {echo "API-V1";});
				Route::on('/info', function () {echo "API-V1-INFO";});
			});
		})
			->before(function () {echo 'AA-';})
			->after(function () {echo '-BB';});

		Route::dispatch('/api2/v1/info', 'get');
		$this->assertEquals(Response::body(), 'AA-API-V1-INFO-BB');
	}

}
