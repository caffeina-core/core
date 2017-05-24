<?php
require __DIR__."/../vendor/autoload.php";

use Core\{
  Route,
  Response
};

Route::any('/',function(){

  Response::push('/test/3.js','script');
  Response::push('/test/2.js','script');
  Response::push('http://www.unheap.com/wp-includes/js/jquery/jquery.js?ver=1.12.4','script');
  /**/
  return "Hello!<script src='http://www.unheap.com/wp-includes/js/jquery/jquery.js?ver=1.12.4'></script><script src='/test/2.js'></script><script src='/test/3.js'></script><script src='/test/4.js'></script><script src='/test/5.js'></script>";
});

Route::any('/test/(:digit).js',function($digit=1){
  Response::expire('max');
  return "console.log('Weeee! - $digit')";
});

Route::dispatch();