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
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::on('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasGet() {
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::get('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasPost() {
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::post('/', function () {
			return "index";
		});
		Route::dispatch('/', 'post');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testRouteNotFound() {
    Options::set('core.response.autosend', false);
		Response::clean();
		$test = $this;
		Event::on(404, function () use (&$test) {
			$test->assertEquals(404, 404);
		});
		Route::dispatch('/this/is/a/404', 'get');
		Event::off(404);
	}

	public function testWildcardMethod() {
    Options::set('core.response.autosend', false);
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
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::on('/post/:a/:b', function ($a, $b) {return "$b-$a";});
		Route::dispatch('/post/1324/fefifo', 'get');
		$this->assertEquals(Response::body(), 'fefifo-1324');
	}

	public function testMiddlewares() {
    Options::set('core.response.autosend', false);
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
    Options::set('core.response.autosend', false);
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

  public function testNullParamerGroupIndex() {
    Options::set('core.response.autosend', false);
    Response::clean();
    $this->mock_request('/aaaaaa', 'get');
    $api = Route::group('/aaaaaa', function (){
      Route::on('/(:id)', function ($test=0) {echo "$test-API-INFO";});
    });
    Route::dispatch('/aaaaaa', 'get');
    $this->assertEquals(Response::body(), '0-API-INFO');
  }

  public function testGroupsSkipping() {
    Event::off(404);
    Options::set('core.response.autosend', false);
    Response::clean();
    $this->mock_request('/not_right', 'get');
    $self = $this;
    $api = Route::group('/not', function () use ($self) {
      $self->assertTrue(false,"This assert must be skipped to be ok."); // This is an error!
    });
    Route::dispatch('/not_right', 'get');
    $this->assertTrue(true); // Good.
  }

	public function testGroupsMiddlewares() {
    Options::set('core.response.autosend', false);
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


  public function testGroupsExtraction() {
    Options::set('core.response.autosend', false);
    Response::clean();
    $this->mock_request('/item/1/info', 'get');

    Route::group("/item/:id",function($id){
     
      Route::on("/",function() use ($id){
        return "$id";
      });

      Route::on("/:field",function($field) use ($id){
        return "{$id}->{$field}";
      });

    });

    Route::dispatch('/item/1/info', 'get');
    $this->assertEquals('1->info', Response::body());

    Response::clean();
    $this->mock_request('/ritem/1/', 'get');

    Route::group("/ritem(/:id)",function($id){
     
      Route::on("/",function() use ($id){
        return "$id";
      });

      Route::on("/:field",function($field) use ($id){
        return "{$id}->{$field}";
      });

    });

    Route::dispatch('/ritem/1/', 'get');
    $this->assertEquals('1', Response::body());
  }



}
