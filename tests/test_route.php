<?php

function mock_request($uri, $method){
    Filter::remove('core.request.method');
    Filter::remove('core.request.URI');
    Filter::add('core.request.URI',function($x)use($uri){return $uri;});
    Filter::add('core.request.method',function($x)use($method){return $method;});
}

Response::clean();
Route::on('/',function(){ return "index"; });
Route::dispatch('/','get');
test(Response::body() == 'index','Route','Basic');


Response::clean();
Event::on(404,function(){ test(1,'Route','404'); });
Route::dispatch('/this/is/a/404','get');
Event::off(404);


Response::clean();
Route::any('/any',function(){ return "ANY"; });
Route::dispatch('/any','patch');
test(Response::body() == 'ANY','Route','Wildcard method');


Response::clean();
Route::on('/post/:a/:b',function($a,$b){ return "$b-$a"; });
Route::dispatch('/post/1324/fefifo','get');
test(Response::body() == 'fefifo-1324','Route','Parameter Extraction');


Response::clean();
Route::on('/middle',function(){ return "-Test-"; })
    ->before (function(){Response::text('AA');})
    ->before (function(){Response::text('B');})
    ->after  (function(){Response::text('AA');})
    ->after  (function(){Response::text('B');})
;
Route::dispatch('/middle','get');
test(Response::body() == 'BAA-Test-AAB','Route','Middlewares');

Response::clean();
mock_request('/api1/v1/info', 'get');
$api = Route::group('/api1',function(){
    Route::on('/info',function(){ echo "API-INFO"; });
    Route::group('/v1',function(){
        Route::on('/',function(){ echo "API-V1"; });
        Route::on('/info',function(){ echo "API-V1-INFO"; });        
    });
});

Route::dispatch('/api1/v1/info','get');
test(Response::body() == 'API-V1-INFO','Route','Group routing');

Response::clean();
mock_request('/api2/v1/info', 'get');
Route::group('/api2',function(){
    Route::on('/info',function(){ echo "API-INFO"; });
    Route::group('/v1',function(){
        Route::on('/',function(){ echo "API-V1"; });
        Route::on('/info',function(){ echo "API-V1-INFO"; });        
    });
})
->before (function(){Response::text('AA-');})
->after  (function(){Response::text('-BB');});

Route::dispatch('/api2/v1/info','get');
test(Response::body() == 'AA-API-V1-INFO-BB','Route','Nested group middlewares');

