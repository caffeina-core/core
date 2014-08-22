<?php

/**
 * Request
 *
 * Handles the HTTP request for the current execution.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class Request {
  use Module;
  protected static $body;

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
   * Retrive a value from generic input (from the $_POST array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   *
   * @return Object The returned value or $default.
   */
  public static function post($key=null,$default=null){
    return $key ? (isset($_POST[$key]) ? $_POST[$key] : (is_callable($default)?call_user_func($default):$default))  : $_POST;
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
    return $key ? (isset($_GET[$key]) ? $_GET[$key] : (is_callable($default)?call_user_func($default):$default))  : $_GET;
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
    return $key ? (isset($_COOKIE[$key]) ? $_COOKIE[$key] : (is_callable($default)?call_user_func($default):$default))  : $_COOKIE;
  }

  /**
   * Returns the current request URL, complete with host and protocol.
   *
   * @return string
   */
  public static function URL(){
    $host = isset($_SERVER['HOSTNAME'])?$_SERVER['HOSTNAME']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:($_SERVER['HTTP_HOST']));
    return 'http' . (empty($_SERVER['HTTPS'])?'':'s') . '://' . $host . static::URI(false);
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
    return $key ? (isset($_SERVER[$key]) ? $_SERVER[$key] : (is_callable($default)?call_user_func($default):$default))  : $_SERVER;
  }

  /**
   * Returns the base request URL, complete with host and protocol.
   *
   * @return string
   */
  public static function baseURL(){
    return 'http' . (empty($_SERVER['HTTPS'])?'':'s') . '://' . $_SERVER['HOSTNAME'] . static::baseURI();
  }

  /**
   * Returns the current request URI.
   *
   * @param  boolean $relative If true, trim the URI relative to the application index.php script. 
   * 
   * @return string
   */
  public static function URI($relative=true){
    // On some web server configurations PHP_SELF is not populated.
    $self = $_SERVER['SCRIPT_NAME'] ?: $_SERVER['PHP_SELF'];
    // Search REQUEST_URI in $_SERVER
    $serv_uri = empty($_SERVER['PATH_INFO'])
      ? ( empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['REQUEST_URI'] : $_SERVER['ORIG_PATH_INFO'] )
      : $_SERVER['PATH_INFO'];
    $uri = strtok($serv_uri,'?');
    $uri = ($uri == $self) ? '/' : $uri;

    // Add a filter here, for URL rewriting
    $uri = Filter::with('route.requestURI',$uri);

    $uri = rtrim($uri,'/');

    if($relative){
      $base = rtrim(dirname($self),'/');
      $uri = str_replace($base,'',$uri);
    }

    return $uri ?: '/';
  }

  /**
   * Returns the base request URI.
   *
   * @return string
   */
  public static function baseURI(){
    // On some web server configurations PHP_SELF is not populated.
    $self = $_SERVER['PHP_SELF'] ?: $_SERVER['SCRIPT_NAME'];
    $uri = dirname($self);
    // Add a filter here, for URL rewriting
    $uri = Filter::with('route.requestURI',$uri);

    $uri = rtrim($uri,'/');
    return $uri ?: '/';
  }

  /**
   * Returns the HTTP Method
   *
   * @return string
   */
  public static function method(){
   return strtolower($_SERVER['REQUEST_METHOD']);
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
    if(null===static::$body){
      $json = (isset($_SERVER['HTTP_CONTENT_TYPE'])&&$_SERVER['HTTP_CONTENT_TYPE']=='application/json') 
           || (isset($_SERVER['CONTENT_TYPE'])&&$_SERVER['CONTENT_TYPE']=='application/json');
      
      static::$body = $json ? json_decode(file_get_contents("php://input")) : file_get_contents("php://input");
    }
    return $key ? (isset(static::$body->$key) ? static::$body->$key : (is_callable($default)?call_user_func($default):$default))  : static::$body;
  }

}
