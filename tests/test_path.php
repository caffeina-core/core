<?php
include_once 'common.php';

Response::clean();
Path::on('/',function(){ echo "index"; });
Path::dispatch('/','get');
test(Response::body() == 'index','Path','Basic');


Response::clean();
Event::on(404,function(){ test(1,'Path','404'); });
Path::dispatch('/this/is/a/404','get');
Event::off(404);


Response::clean();
Path::any('/any',function(){ echo "ANY"; });
Path::dispatch('/any','patch');
test(Response::body() == 'ANY','Path','Wildcard method');


Response::clean();
Path::on('/post/:a/:b',function($a,$b){ echo "$b-$a"; });
Path::dispatch('/post/1324/fefifo','get');
test(Response::body() == 'fefifo-1324','Path','Parameter Extraction');


Response::clean();
Path::on('/middle',function(){ echo "-Test-"; })
    ->before (function(){Response::text('AA');})
    ->before (function(){Response::text('B');},200)
    ->after  (function(){Response::text('AA');})
    ->after  (function(){Response::text('B');})
;
Path::dispatch('/middle','get');
test(Response::body() == 'BAA-Test-AAB','Path','Middlewares');


Response::clean();

$api = Path::group('/api',function(){
    Path::on('/info',function(){ echo "API-INFO"; });
    Path::group('/v1',function(){
        Path::on('/',function(){ echo "API-V1"; });
        Path::on('/info',function(){ echo "API-V1-INFO"; });        
    });
});

Path::dispatch('/api/info','get');
test(Response::body() == 'API-INFO','Path','Group routing');

Response::clean();
Path::dispatch('/api/v1/','get');
test(Response::body() == 'API-V1','Path','Nested group index');

Response::clean();
Path::dispatch('/api/v1/info','get');
test(Response::body() == 'API-V1-INFO','Path','Nested group routing');


$api->before(function(){
    Response::text('|PING|');
});
$api->after(function(){
    Response::text('|PONG|');
});
Response::clean();
Path::dispatch('/api/v1/info','get');
test(Response::body() == '|PING|API-V1-INFO|PONG|','Path','Nested group middlewares');


Response::clean();
Path::map('/multi/:item',[
    'get'      => function($item){ echo 'map-get:'.$item; },
    'post'     => function($item){ echo 'map-post:'.$item; },
    'delete'   => function($item){ echo 'map-delete:'.$item; },
]);
Path::dispatch('/multi/pass','post');
test(Response::body() == 'map-post:pass','Path','Method map');


Response::clean();
Path::on('/other/method')->with(function(){ echo "OK-PUT"; },'put');
Path::dispatch('/other/method/','put');
test(Response::body() == 'OK-PUT','Path','Method callback');



