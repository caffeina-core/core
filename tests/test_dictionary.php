<?php

class TestDict extends Dictionary {}

$array = [
    'a' => 123,
    'b' => 'hello',
    'c' => [
        'r' => '#f00',
        'g' => '#0f0',
        'b' => '#00f',
    ],   
];


test(TestDict::all() == [],'Dictionary','Init');

TestDict::load($array);
test(TestDict::all() == $array,'Dictionary','Load');

TestDict::clear();
test(TestDict::all() == [],'Dictionary','Clear');


TestDict::set('a',999);
test(TestDict::all()['a'] == 999,'Dictionary','Set');
test(TestDict::get('a') == 999,'Dictionary','Get');
test(TestDict::exists('a'),'Dictionary','Exists');
test(!TestDict::exists('not-a'),'Dictionary','Exists (unknown key)');

TestDict::merge($array,true);
test(TestDict::all()['a'] == 999,'Dictionary','Left Merge');

TestDict::clear();
TestDict::set('a',999);
TestDict::merge($array);
test(TestDict::all()['a'] == 123,'Dictionary','Right Merge');


test(TestDict::get('i-dont-exists','OK') == 'OK','Dictionary','Default on get fail (Immediate)');
test(TestDict::get('i-dont-exists',function(){return 'OK';}) == 'OK','Dictionary','Default on get fail (Callable)');


test(TestDict::set('a.b.c.d',1) === 1,'Dictionary','Set from path');
test(TestDict::get('a.b.c.d',0) === 1,'Dictionary','Get from path');
