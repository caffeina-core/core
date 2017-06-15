<?php

/**
 * Errors
 *
 * Handle system and application errors.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core;

abstract class Errors {
    use Module, Events;

    const SIMPLE = 0,
          HTML   = 1,
          SILENT = 2,
          JSON   = 3;

    static $mode = self::SILENT;

    final public static function capture($tracing_level=null){
      if($tracing_level!==null) error_reporting($tracing_level);
      set_error_handler(__CLASS__.'::traceError');
      set_exception_handler(__CLASS__.'::traceException');
    }

    final public static function mode($mode=null){
      return $mode ? self::$mode=$mode : self::$mode;
    }

    final public static function traceError($errno, $errstr, $errfile=null, $errline=null){
      // This error code is not included in error_reporting
      if (!(error_reporting() & $errno)) return;
      switch ( $errno ) {
        case E_USER_ERROR:
            $type = 'Fatal';
        break;
        case E_USER_WARNING:
        case E_WARNING:
            $type = 'Warning';
        break;
        case E_USER_NOTICE:
        case E_NOTICE:
        case E_STRICT:
            $type = 'Notice';
        break;
        default:
            $type = 'Error';
        break;
      }
      $e = new \ErrorException($type.': '.$errstr, 0, $errno, $errfile, $errline);
      $error_type = strtolower($type);
      $chk_specific = array_filter(
                      (array)static::trigger($error_type,$e)
                    );
      $chk_general  = array_filter(
                      (array)static::trigger('any',$e)
                    );
      if (! ($chk_specific || $chk_general) ) static::traceException($e);
      return true;
    }

    final public static function traceException($e){
      switch(self::$mode){
          case self::HTML :
              echo '<pre class="app error exception"><code>',$e->getMessage(),'</code></pre>',PHP_EOL;
              break;
          case self::JSON :
              echo json_encode(['error' => $e->getMessage()]);
              break;
          case self::SILENT :
              // Don't echo anything.
              break;
          case self::SIMPLE :
          default:
              echo $e->getMessage(),PHP_EOL;
              break;
      }
      return true;
    }

}
