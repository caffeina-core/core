<?php

/**
 * Error
 *
 * Handle system and application errors.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Error {

    public static function capture(){

      set_error_handler(function($errno, $errstr, $errfile, $errline ) {
          throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
      });

      set_error_handler(__CLASS__.'::traceError');
    }

    public static function traceError($errno,$errstr,$errfile=null,$errline=null){
      // This error code is not included in error_reporting
      if (!(error_reporting() & $errno)) return;
      switch ($errno) {
        case E_USER_ERROR:
            echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
            exit(1);
            break;

        case E_USER_WARNING:
            echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
            break;

        case E_USER_NOTICE:
            echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
            break;

        default:
            echo "Unknown error type: [$errno] $errstr<br />\n";
            break;
      }

      /* Don't execute PHP internal error handler */
      return true;
    }

    public static function traceException($e){
      
      /* Don't execute PHP internal error handler */
      return true;
    }

    public static function onFatal(callable $listener){
      Event::on('core.error.fatal',$listener);
    }

    public static function onWarning(callable $listener){
      Event::on('core.error.warning',$listener);
    }

    public static function on(callable $listener){
      Event::on('core.error.warning',$listener);
    }

}
