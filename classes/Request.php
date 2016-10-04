<?php

/**
 * Request
 *
 * Handles the HTTP request for the current execution.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class Request {
  use Module;

  protected static $body,
                   $accepts;

  /**
   * Handle Content Negotiation requests
   *
   * @param  string $key The name of the negotiation subject
   * @param  string $choices A query string for the negotiation choices (See RFC 7231)
   *
   * @return Object The preferred content if $choices is empty else return best match
   */
  public static function accept($key='type',$choices='') {
    if (null === static::$accepts) static::$accepts = [
      'type'     => new Negotiation(isset($_SERVER['HTTP_ACCEPT'])          ? $_SERVER['HTTP_ACCEPT']          : ''),
      'language' => new Negotiation(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''),
      'encoding' => new Negotiation(isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : ''),
      'charset'  => new Negotiation(isset($_SERVER['HTTP_ACCEPT_CHARSET'])  ? $_SERVER['HTTP_ACCEPT_CHARSET']  : ''),
    ];
    return empty(static::$accepts[$key])
      ? false
      : ( empty($choices)
           ? static::$accepts[$key]->preferred()
           : static::$accepts[$key]->best($choices)
      );
  }

  /**
   * Retrive a value from generic input (from the $_REQUEST array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function input($key=null,$default=null){
    return $key ? (isset($_REQUEST[$key]) ? new Object($_REQUEST[$key]) : (is_callable($default)?call_user_func($default):$default))  : new Object($_REQUEST[$key]);
  }

  /**
   * Retrive a value from environment (from the $_ENV array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function env($key=null,$default=null){
    return $key ? (filter_input(INPUT_ENV,$key) ?: (is_callable($default)?call_user_func($default):$default))  : $_ENV;
  }

  /**
   * Retrive a value from server (from the $_SERVER array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function server($key=null,$default=null){
    return $key ? (isset($_SERVER[$key]) ? $_SERVER[$key] : (is_callable($default)?call_user_func($default):$default)) : $_SERVER;
  }

  /**
   * Retrive a value from generic input (from the $_POST array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function post($key=null,$default=null){
    return $key ? (filter_input(INPUT_POST,$key) ?: (is_callable($default)?call_user_func($default):$default)) : $_POST;
  }

  /**
   * Retrive a value from generic input (from the $_GET array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function get($key=null,$default=null){
    return $key ? (filter_input(INPUT_GET,$key) ?: (is_callable($default)?call_user_func($default):$default)) : $_GET;
  }

  /**
   * Retrive uploaded file (from the $_FILES array)
   * Returns all uploaded files if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function files($key=null,$default=null){
    return $key ? (isset($_FILES[$key]) ? $_FILES[$key] : (is_callable($default)?call_user_func($default):$default))  : $_FILES;
  }

  /**
   * Retrive cookie (from the $_COOKIE array)
   * Returns all cookies if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function cookie($key=null,$default=null){
    return $key ? (filter_input(INPUT_COOKIE,$key) ?: (is_callable($default)?call_user_func($default):$default))  : $_COOKIE;
  }

  /**
   * Returns the current host and port (omitted if port 80), complete with protocol (pass `false` to omit).
   *
   * @return string
   */
  public static function host($protocol=true){
    switch(true){
      case !empty($_SERVER['HTTP_X_FORWARDED_HOST']) :
        $host = trim(substr(strrchr($_SERVER['HTTP_X_FORWARDED_HOST'],','),1) ?: $_SERVER['HTTP_X_FORWARDED_HOST']);
      break;
      case !empty($_SERVER['HTTP_HOST'])    : $host = $_SERVER['HTTP_HOST'];   break;
      case !empty($_SERVER['SERVER_NAME'])  : $host = $_SERVER['SERVER_NAME']; break;
      case !empty($_SERVER['HOSTNAME'])     : $host = $_SERVER['HOSTNAME'];    break;
      default                               : $host = 'localhost';             break;
    }
    $host = explode(':',$host,2);
    $port = isset($host[1]) ? (int)$host[1] : (isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:80);
    $host = $host[0] . (($port && $port != 80) ? ":$port" : '');
    if ($port == 80) $port = '';
    return ($protocol ? 'http' . (!empty($_SERVER['HTTPS'])&&(strtolower($_SERVER['HTTPS'])!=='off')?'s':'') . '://' : '')
           . Filter::with('core.request.host',$host);
  }

  /**
   * Returns the current request URL, complete with host and protocol.
   *
   * @return string
   */
  public static function URL(){
    return static::host(true) . static::URI(false);
  }

  /**
   * Retrive header
   * Returns all headers if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or null.
   */
  public static function header($key=null,$default=null){
    $key = 'HTTP_'.strtr(strtoupper($key),'-','_');
    return $key ? (isset($_SERVER[$key])? $_SERVER[$key] : (is_callable($default)?call_user_func($default):$default)) : $_SERVER;
  }

  /**
   * Returns the current request URI.
   *
   * @param  boolean $relative If true, trim the URI relative to the application index.php script.
   *
   * @return string
   */
  public static function URI($relative=true){
    // On some web server configurations SCRIPT_NAME is not populated.
    $self = !empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'];
    // Search REQUEST_URI in $_SERVER
    switch(true){
      case !empty($_SERVER['REQUEST_URI']):    $serv_uri = $_SERVER['REQUEST_URI']; break;
      case !empty($_SERVER['ORIG_PATH_INFO']): $serv_uri = $_SERVER['ORIG_PATH_INFO']; break;
      case !empty($_SERVER['PATH_INFO']):      $serv_uri = $_SERVER['PATH_INFO']; break;
      default:                                 $serv_uri = '/'; break;
    }
    $uri = strtok($serv_uri,'?');
    $uri = ($uri == $self) ? '/' : $uri;

    // Add a filter here, for URL rewriting
    $uri = Filter::with('core.request.URI',$uri);

    $uri = rtrim($uri,'/');

    if ($relative){
      $base = rtrim(dirname($self),'/');
      $uri = str_replace($base,'',$uri);
    }

    return $uri ?: '/';
  }

  /**
   * Returns the current base URI (The front-controller directory)
   *
   * @return string
   */
  public static function baseURI(){
    return dirname(empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']) ?: '/';
  }

  /**
   * Returns the HTTP Method
   *
   * @return string
   */
  public static function method(){
   return Filter::with('core.request.method',strtolower(empty($_SERVER['REQUEST_METHOD'])?'get':$_SERVER['REQUEST_METHOD']));
  }

  /**
   * Returns the remote IP
   *
   * @return string
   */
  public static function IP(){
   switch(true){
     case !empty($_SERVER['HTTP_X_FORWARDED_FOR']):
       $ip = trim(substr(strrchr($_SERVER['HTTP_X_FORWARDED_FOR'],','),1) ?: $_SERVER['HTTP_X_FORWARDED_FOR']);
     break;
     case !empty($_SERVER['HTTP_X_FORWARDED_HOST']):
       $ip = trim(substr(strrchr($_SERVER['HTTP_X_FORWARDED_HOST'],','),1) ?: $_SERVER['HTTP_X_FORWARDED_HOST']);
     break;
     case !empty($_SERVER['REMOTE_ADDR']):    $ip = $_SERVER['REMOTE_ADDR']; break;
     case !empty($_SERVER['HTTP_CLIENT_IP']): $ip = $_SERVER['HTTP_CLIENT_IP']; break;
     default:                                 $ip = ''; break;
   }
   return Filter::with('core.request.IP',$ip);
  }

  /**
   * Returns the remote UserAgent
   *
   * @return string
   */
  public static function UA(){
   return Filter::with('core.request.UA',strtolower(empty($_SERVER['HTTP_USER_AGENT'])?'':$_SERVER['HTTP_USER_AGENT']));
  }

  /**
   * Returns request body data, convert to object if content type is JSON
   * Gives you all request data if you pass `null` as $key
   *
   * @param  string $key The name of the key requested
   *
   * @return mixed The request body data
   */
  public static function data($key=null,$default=null){
    if (null===static::$body){
      $json = (false !== stripos(empty($_SERVER['HTTP_CONTENT_TYPE'])?'':$_SERVER['HTTP_CONTENT_TYPE'],'json'))
           || (false !== stripos(empty($_SERVER['CONTENT_TYPE'])?'':$_SERVER['CONTENT_TYPE'],'json'));
      if ($json) {
        static::$body = json_decode(file_get_contents("php://input"));
      } else {
       if (empty($_POST)) {
          static::$body = file_get_contents("php://input");
        } else {
          static::$body = (object)$_POST;
        }
      }
    }
    return $key ? (isset(static::$body->$key) ? static::$body->$key : (is_callable($default)?call_user_func($default):$default))  : static::$body;
  }

}
