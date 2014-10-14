<?php


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



