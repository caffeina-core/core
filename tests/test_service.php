<?php

Service::single('email',function() {
    return "EMAIL SERVICE";
});
test(Service::email() . Service::email() == "EMAIL SERVICEEMAIL SERVICE",'Service','Simple service container');



Service::single('test',function($data) {
    return (object)["data" => $data];
});
test(Service::test('--TEST--')->data == "--TEST--",'Service','Constructor');


test(Service::test()->data == "--TEST--",'Service','Service persistence');
test(Service::test("NOT ME!")->data != "NOT ME!",'Service','Service persistence even with new constructor call');


Service::multiple('foos',function($bar) {
    return (object)["data" => $bar];
});
test(
  implode('',[
    Service::foos('A')->data,
    Service::foos('B')->data,
    Service::foos('C')->data,
  ]) == "ABC",
'Service','Factory : Build multiple service instances');

