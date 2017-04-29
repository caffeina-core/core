<?php

/**
 * CLI
 *
 * CLI commands routing.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core;

class CLI {
  use Module,
      Events,
      Filters;

  protected static $args,
                   $commands,
                   $cursor;

  public static function args($_argv=null){
    if (null === $_argv) $_argv = $GLOBALS['argv'];

    $addArg = function($arg_name, $value){
      $leaf =& static::$args->opts->$arg_name;
      if (isset($leaf)){
        if (!is_array($leaf)) $leaf = ["$leaf",$value];
        else $leaf[] = $value;
      } else $leaf = $value;
    };

    if (!static::$args){
      static::$args = (object)['opts'=>(object)[],'args'=>[],'extra'=>''];
      $args = array_slice($_argv, 1);
      while (list($argi, $arg_x) = each($args)){
        if ($arg_x == '--'){
          // Skip parsing args
          static::$args->extra = implode(" ", array_slice($args, $argi+1));
          break;
        } else if ($arg_x{0} == '-') {
          // Single or Multiple options
          $arg_name = substr($arg_x, 1);
          if ($arg_x{1} == '-') {
            // Named option
            $arg_name = substr($arg_name, 1);
            if (strpos($arg_name, '=') !== false){
              if (substr($arg_name, -1,1)=='='){
                // Direct assignment, quoted value : --option="foo bar"
                $arg_name = rtrim($arg_name,'=');
                $value = current($args);
                next($args);
              } else {
                // Direct assignment : --option=123
                list($arg_name, $value) = explode('=', substr($arg_x,2), 2);
              }
            } else {
              if (substr($arg_name,0,3)=='no-'){
                // Inverted option (FALSE): --no-option
                $value = 0;
                $arg_name = substr($arg_name, 3);
              } else {
                // Simple option (TRUE): --option
                $value = 1;
              }
            }
            $addArg($arg_name, $value);
          } else {
            // Multiple single options : -abCD
            foreach(str_split($arg_name, 1) as $flag){
              static::$args->opts->$flag = true;
            }
          }
        } else {
          // Simple argument
          static::$args->args[] = $arg_x;
        }
      }
    }
    return static::$args;
  }

  public static function run($cmd=null){
    $inputs       = static::args();
    $command_name = array_shift($inputs->args);
    $command      = static::$commands[$command_name] ?? null;
    if ($command) {
      $command->exec($inputs);
    } else {
      static::trigger('usage');
      exit(1);
    }
  }

  public static function get($key){
    return isset(static::$args->args[$key]) ? static::$args->args[$key] : null;
  }

  public static function define($command_name){
    return static::$commands[$command_name] = new CLI\Command($command_name);
  }

  public static function cursor(...$params){
    return static::$cursor ?: static::$cursor = new CLI\Cursor();
  }

  public static function progress(...$params){
    return new CLI\UI\ProgressBar(...$params);
  }

  public static function logs(...$params){
    return new CLI\UI\Logs(...$params);
  }

  public static function color(...$params){
    return CLI\Colors::get(...$params);
  }

  public static function write(...$texts){
    foreach($texts as $txt) echo CLI\Colors::colorize($txt);
  }

  public static function line(){
    echo str_repeat('-',(int)`tput cols`-1)."\n";
  }

}