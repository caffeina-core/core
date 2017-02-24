<?php

/**
 * Work
 *
 * Cooperative multitasking via coroutines.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @reference http://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

class Work {
  use Module;

  protected static $pool = null,
                   $workers,
                   $lastID = 0,
                   $inited_shutdown = false;

  public static function add($id, $job=null){
    self::$pool or ( self::$pool = new \SplQueue() );
    if(is_callable($id) && $job===null){
      $job = $id;
      $id = ++self::$lastID;
    }
    $task = new TaskCoroutine($id, $job instanceof \Generator ? $job : $job());
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

  /**
   * Defer callback execution after script execution
   * @param callable $callback The deferred callback
   */
  public static function after(callable $callback){
    static::$inited_shutdown || static::install_shutdown();
    Event::on('core.shutdown', $callback);
  }

  /**
   * Single shot defer handeler install
   */
  protected static function install_shutdown(){
    if (static::$inited_shutdown) return;

    // Disable time limit
    set_time_limit(0);

    register_shutdown_function(function(){
      function_exists('fastcgi_finish_request') && fastcgi_finish_request();
      Event::trigger('core.shutdown');
    });

    static::$inited_shutdown = true;
  }
}

class TaskCoroutine {

    protected $id,
              $coroutine,
              $passValue = null,
              $beforeFirstYield = true;
    protected static $inited_shutdown = false;

    public function __construct($id, \Generator $coroutine) {
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
