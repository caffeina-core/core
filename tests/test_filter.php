<?php

Filter::add('test',function($text) {
    return strtoupper($text);
});
test(Filter::with('test','alpha') == 'ALPHA','Filter','Filter add.');


Filter::add('test',function($text) {
    return '_' . $text . '_';
});
test(Filter::with('test','alpha') == '_ALPHA_','Filter','Filter multiple.');


Filter::remove('test');
test(Filter::with('test','alpha') == 'alpha','Filter','Filter remove.');


