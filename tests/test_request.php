<?php

$request_set_clear_data = function(){
	$_GET 		= [];
	$_POST 		= [];
	$_COOKIE 	= [];
	$_FILES 	= [];
	$_REQUEST 	= [];
	$_ENV 		= [];
};

$request_set_get_data = function($data){
	$_GET = $data;
};

$request_set_post_data = function($data){
	$_POST = $data;
};

$request_set_cookie_data = function($data){
	$_COOKIE = $data;
};

$request_set_files_data = function($data){
	$_FILES = $data;
};

$request_set_env_data = function($data){
	$_ENV = $data;
};

$request_set_get_data(['alpha'=>'beta']);
test(Request::get()['alpha'] == 'beta','Request','Get');

$request_set_post_data(['alpha'=>'beta']);
test(Request::post()['alpha'] == 'beta','Request','Post');

$request_set_cookie_data(['alpha'=>'beta']);
test(Request::cookie()['alpha'] == 'beta','Request','Cookie');

$request_set_files_data(['alpha'=>'beta']);
test(Request::files()['alpha'] == 'beta','Request','Files');

$request_set_env_data(['alpha'=>'beta']);
test(Request::env()['alpha'] == 'beta','Request','Env');

test(Request::URL() == 'http:///','Request','URL');
test(Request::URI() == '/','Request','URI');


