<?php
include dirname(__DIR__).'/classes/Loader.php';
error_reporting(-1);
set_error_handler(function(){});
set_exception_handler(function(){});

// Common Test Utilities
$test_stats = ["PASS"=>0,"FAIL"=>0,"GROUPS"=>[]];
$test_verbose = true;

function test($condition,$group='COMMON',$message='Error') {
  global $test_stats,$test_verbose;
  $test_stats[$result=$condition?"PASS":"FAIL"]++;
  $test_stats['GROUPS'][$group][] = "[$result]: $group: $message";
  if($test_verbose && !$result)
    echo "[FAIL]: $group: $message\n";
}

