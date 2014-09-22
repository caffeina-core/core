<?php
include_once 'common.php';
echo "Test started on ",date('Y-m-d H:i:s'),".\n";
foreach (glob('test_*.php') as $testfile) include $testfile;
echo "Test ended on ",date('Y-m-d H:i:s'),".\n";
echo "Results: ",$test_stats["PASS"]," tests passed, ",$test_stats["FAIL"]," tests failed.\n";
echo "Done.\n";