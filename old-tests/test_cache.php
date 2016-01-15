<?php

Cache::using('memory');

Cache::set('test','ALPHA');
test(Cache::get('test') == 'ALPHA','Cache','Set/Get');


test(Cache::get('test2','BETA') == 'BETA','Cache','Get unknown');
test(Cache::get('test2') == 'BETA','Cache','Get setted default');


test(Cache::get('test3',function(){ return "SLOW_DATA"; }) == 'SLOW_DATA','Cache','Get unknown -> default callback');


