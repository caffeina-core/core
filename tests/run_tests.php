<?php
include_once 'common.php';

date_default_timezone_set('Europe/Rome');

echo "Test started on ",date('Y-m-d H:i:s'),".\n\n";
$tests = glob(__DIR__.'/test_*.php');

foreach ($tests as $testfile) {
	echo "\n[ ".strtoupper(strtok(basename($testfile),'.'))." ]".str_pad('',60,'=')."\n\n";
	include $testfile;
}
echo "\nTest ended on ",date('Y-m-d H:i:s'),".\n";

echo "Results: ",$test_stats["PASS"]," tests passed, ",$test_stats["FAIL"]," tests failed.\n";

echo "Done.\n";
exit($test_stats["FAIL"]?1:0);

