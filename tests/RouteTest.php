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
    Route::reset();
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::on('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasGet() {
    Route::reset();
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::get('/', function () {
			return "index";
		});
		Route::dispatch('/', 'get');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testAliasPost() {
    Route::reset();
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::post('/', function () {
			return "index";
		});
		Route::dispatch('/', 'post');
		$this->assertEquals(Response::body(), 'index');
	}

	public function testRouteNotFound() {
    Route::reset();
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
    Route::reset();
    Options::set('core.response.autosend', false);
		Route::any('/any', function () {return "ANY";});
		Response::clean();
		Route::dispatch('/any', 'patch');
		$this->assertEquals('ANY', Response::body(),'patch /any');
		Response::clean();
		Route::dispatch('/any', 'get');
    $this->assertEquals('ANY', Response::body(),'get /any');
		Response::clean();
		Route::dispatch('/any', 'post');
    $this->assertEquals('ANY', Response::body(),'post /any');
	}

	public function testParameterExtraction() {
    Route::reset();
    Options::set('core.response.autosend', false);
		Response::clean();
		Route::on('/post/:a/:b', function ($a, $b) {return "$b-$a";});
		Route::dispatch('/post/1324/fefifo', 'get');
		$this->assertEquals('fefifo-1324', Response::body());
	}

	public function testMiddlewares() {
    Route::reset();
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
    Route::reset();
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
    Route::reset();
    Options::set('core.response.autosend', false);
    Response::clean();
    $this->mock_request('/aaaaaa', 'get');
    $api = Route::group('/aaaaaa', function (){
      Route::on('/(:id)', function ($test=0) {echo "$test-API-INFO";});
    });
    Route::dispatch('/aaaaaa', 'get');
    $this->assertEquals('0-API-INFO',Response::body());
  }

  public function testGroupsSkipping() {
    Route::reset();
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
    Route::reset();
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

  public function testStaticGroupsNesting() {
      Route::reset();
      Event::off(404);
      Options::set('core.response.autosend', false);
      Response::clean();
      $URI = '/r_a/r_b/r_c/r_d';
      $this->mock_request($URI, 'get');
      Route::group('/r_a', function () {
        Route::group('/r_b', function () {
          Route::group('/r_c', function () {
            Route::group('/r_d', function () {
              Route::on('/',function(){
                return "OK-STATIC";
              });
            });
          });
        });
      });
      Route::dispatch($URI, 'get');
      $this->assertEquals('OK-STATIC', Response::body());
    }

  public function testGroupsExtraction() {
    Route::reset();
    Options::set('core.response.autosend', false);
    Response::clean();
    $this->mock_request('/item/1/info', 'get');

    Route::group("/item(/:id)",function($id){

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


   public function testDynamicGroupsNesting() {
      Route::reset();

      Event::off(404);
      Options::set('core.response.autosend', false);
      Response::clean();

      $URI = '/x_a/x_b/x_c/x_d';
      $this->mock_request($URI, 'get');

      Route::group('/x_:a', function ($a) {
        Route::group('/x_:b', function ($b) use ($a) {
          Route::group('/x_:c', function ($c) use ($a,$b) {
            Route::group('/x_:d', function ($d) use ($a,$b,$c) {
              Route::on('/',function() use ($a,$b,$c,$d){
                return "OK-DYNAMIC-$a$b$c$d";
              });
            });
          });
        });
      });

      Route::dispatch($URI, 'get');
      $this->assertEquals('OK-DYNAMIC-abcd', Response::body());
    }

   public function testFullyOptionalRoute() {
      Route::reset();

      Options::set('core.response.autosend', false);
      Options::set('core.route.pruning', false);

      Route::on('/', function (){
          return "INDEX";
      });

      Route::on('/(:optional)', function ($optional='0'){
          return "ROOT:OPTIONAL:$optional";
      });

      Route::group('/model', function () {

        Route::on('(/:slug)', function ($slug = null){
          return "SLUG:" . ($slug === null ? 'NULL' : $slug);
        });

        Route::on('/:slug/info', function ($slug){
          return "INFO:FOR:$slug";
        });

      });

      $URI = '/model/test/info';
      Response::clean();
      $this->mock_request($URI, 'get');
      Route::dispatch($URI, 'get');
      $this->assertEquals(Response::body(),'INFO:FOR:test',$URI);

      $URI = '/model';
      Response::clean();
      $this->mock_request($URI, 'get');
      Route::dispatch($URI, 'get');
      $this->assertEquals(Response::body(),'SLUG:NULL',$URI);

      $URI = '/model/test';
      Response::clean();
      $this->mock_request($URI, 'get');
      Route::dispatch($URI, 'get');
      $this->assertEquals(Response::body(),'SLUG:test',$URI);

      $URI = '/';
      Response::clean();
      $this->mock_request($URI, 'get');
      Route::dispatch($URI, 'get');
      $this->assertEquals(Response::body(),'INDEX',$URI);

      $URI = '/foobar';
      Response::clean();
      $this->mock_request($URI, 'get');
      Route::dispatch($URI, 'get');
      $this->assertEquals(Response::body(),'ROOT:OPTIONAL:foobar',$URI);

    }

}
