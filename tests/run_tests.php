<?php
include_once 'common.php';
date_default_timezone_set('Europe/Rome');
echo "Test started on ",date('Y-m-d H:i:s'),".\n\n";
foreach (glob('test_*.php') as $testfile) include $testfile;
echo "\nTest ended on ",date('Y-m-d H:i:s'),".\n";
echo "Results: ",$test_stats["PASS"]," tests passed, ",$test_stats["FAIL"]," tests failed.\n";
echo "Done.\n";