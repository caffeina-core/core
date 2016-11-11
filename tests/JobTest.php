<?php

class JobTest extends PHPUnit_Framework_TestCase {

  public $results = null;

	public function setUp() {
    SQL::off('error');
    SQL::on('error',function($e,$q){
      echo "\nSQL::Error: $e\n$q\n";
    });
    SQL::exec("CREATE TABLE queue (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      type TEXT,
      status TEXT DEFAULT 'PENDING',
      tries INTEGER,
      created_at INTEGER NOT NULL DEFAULT CURRENT_TIMESTAMP,
      scheduled_at INTEGER NULL,
      activated_at INTEGER NULL DEFAULT NULL,
      payload TEXT,
      error TEXT
    )");
    $test = $this;
    Job::register('job1',function($job, $args) use (&$test) {
      $test->results = (object)[
        'type' => 'job1',
        'args' => $args,
        'job'  => $job,
      ];
    });
    Job::register('job2',function($job, $args) use (&$test) {
      $test->results = (object)[
        'type' => 'job2',
        'args' => $args,
        'job'  => $job,
      ];
    });
	}

  public function tearDown() {
    Filter::remove('core.sql.query');
  }

	public function testQueue() {

	  Job::queue('job1');
    $raw_jobs = SQL::all("SELECT * FROM queue");

    var_dump($raw_jobs);die;

		$this->assertTrue(($id1 == 1) && ($id4 == 4));

	}

}
