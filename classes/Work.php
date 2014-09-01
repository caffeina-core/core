<?php

version_compare(PHP_VERSION, '5.5.0', '>=') 
  or trigger_error('Work class need PHP 5.5 or later.',E_USER_ERROR);

/**
 * Work
 *
 * Cooperative multitasking via coroutines.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @reference http://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Work {
  
  protected static $pool = null;
  protected static $workers;
  protected static $lastID = 0;

  public static function add($id,$job=null){
    self::$pool or ( self::$pool = new \SplQueue() );
    if(is_callable($id) && $job===null){
      $job = $id;
      $id = ++self::$lastID;      
    }
    $task = new TaskCoroutine($id, $job instanceof Generator ? $job : $job());
    self::$workers[$id] = $task;
    self::$pool->enqueue($task);
    return $task;
  }

  public static function send($id,$passValue) {
     isset(self::$workers[$id]) && self::$workers[$id]->pass($passValue);
  }
  
  public static function run(){
    self::$pool or ( self::$pool = new \SplQueue() );
    while (!self::$pool->isEmpty()) {
      $task = self::$pool->dequeue();
      $task->run();
      if ($task->complete()) {
         unset(self::$workers[$task->id()]);
      } else {
          self::$pool->enqueue($task);
      }
    }
  }
  
}

class TaskCoroutine {
  
    protected $id;
    protected $coroutine;
    protected $passValue = null;
    protected $beforeFirstYield = true;

    public function __construct($id, Generator $coroutine) {
        $this->id = $id;
        $this->coroutine = $coroutine;
    }

    public function id() {
        return $this->id;
    }

    public function pass($passValue) {
        $this->passValue = $passValue;
    }

    public function run() {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } else {
            $retval = $this->coroutine->send($this->passValue);
            $this->passValue = null;
            return $retval;
        }
    }

    public function complete() {
        return ! $this->coroutine->valid();
    }

}

/*
  Example: Simple workers

  Work::add(function(){
    for ($i = 0; $i <= 20; $i++) {
      echo 'A:',$i,PHP_EOL;
      yield;
    }
  });

  Work::add(function(){
    for ($i = 0; $i <= 10; $i++) {
      echo 'B:',$i,PHP_EOL;
      yield;
    }
  });

  Work::run();

*/

/*
Example : Passing arguments between Workers

Work::add(function(){
  for ($i = 0; $i <= 20; $i++) {
    echo 'A:',$i,PHP_EOL;
    Work::send('B',$i*100);
    yield;
  }
});

Work::add('B',function(){
  for ($i = 0; $i <= 10; $i++) {
    echo 'B:',$i + yield,PHP_EOL;
  }
});

Work::run();

*/
