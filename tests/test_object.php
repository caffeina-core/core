<?php

// Object accessor
$array = [
    'a' => 123,
    'b' => 'hello',
    'c' => [
        'r' => '#f00',
        'g' => '#0f0',
        'b' => '#00f',
    ],   
];

$object = (object)[
    'a' => 123,
    'b' => 'hello',
    'c' => (object)[
        'r' => '#f00',
        'g' => '#0f0',
        'b' => '#00f',
    ],   
];

// Surface
$test = new Object($array, false);

test($test['a']          == 123,'Object','Access as Array from Array');
test($test->b            == 'hello','Object','Access as Object from Array');


// Deep 
$test = new Object($array);

test($test->c['r']       == '#f00','Object','Deep access as Object from Array');
test($test['c']->g       == '#0f0','Object','Deep access as Array from Array');
test($test['c']['b']     == '#00f','Object','Deep access as DoubleArray from Array');
test($test->c->b         == '#00f','Object','Deep access as DoubleObject from Array');

$test = new Object($object);
test($test->c['r']       == '#f00','Object','Deep access as Object from Object');
test($test['c']->g       == '#0f0','Object','Deep access as Array from Object');
test($test['c']['b']     == '#00f','Object','Deep access as DoubleArray from Object');
test($test->c->b         == '#00f','Object','Deep access as DoubleObject from Object');