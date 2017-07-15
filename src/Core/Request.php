<?php

/**
 * Request
 *
 * Handles the HTTP request for the current execution.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2017 - http://caffeina.com
 */

namespace Core;

class Request {
  use Module,
      Filters;

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
  public static function accept($key='type', $choices='') {
    if (null === static::$accepts) static::$accepts = [
      'type'     => new Negotiation($_SERVER['HTTP_ACCEPT']          ?? ''),
      'language' => new Negotiation($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''),
      'encoding' => new Negotiation($_SERVER['HTTP_ACCEPT_ENCODING'] ?? ''),
      'charset'  => new Negotiation($_SERVER['HTTP_ACCEPT_CHARSET']  ?? ''),
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
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function input($key=null, $default=null){
    return $key ? (
             isset($_REQUEST[$key])
             ? new Object($_REQUEST[$key])
             : (is_callable($default) ? call_user_func($default) : $default)
           ) : new Object($_REQUEST[$key]);
  }

  /**
   * Retrive a value from environment (from the $_ENV array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function env($key=null, $default=null){
    return $key ? (
            filter_input(INPUT_ENV, $key)
              ?: (is_callable($default) ? call_user_func($default) : $default)
           ) : $_ENV;
  }

  /**
   * Retrive a value from server (from the $_SERVER array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function server($key=null, $default=null){
    return $key ? (
              $_SERVER[$key] ?? (is_callable($default) ? call_user_func($default) : $default)
            ) : $_SERVER;
  }

  /**
   * Retrive a value from generic input (from the $_POST array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function post($key=null, $default=null){
    return $key ? (
            filter_input(INPUT_POST, $key)
              ?: (is_callable($default) ? call_user_func($default) : $default)
           ) : $_POST;
  }

  /**
   * Retrive a value from generic input (from the $_GET array)
   * Returns all elements if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function get($key=null, $default=null){
    return $key ? (
            filter_input(INPUT_GET, $key)
              ?: (is_callable($default) ? call_user_func($default) : $default)
           ) : $_GET;
  }

  /**
   * Retrive uploaded file (from the $_FILES array)
   * Returns all uploaded files if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function files($key=null, $default=null){
    return $key ? (
            $_FILES[$key] ?? (is_callable($default) ? call_user_func($default) : $default)
           ) : $_FILES;
  }

  /**
   * Retrive cookie (from the $_COOKIE array)
   * Returns all cookies if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or $default.
   */
  public static function cookie($key=null,$default=null){
    return $key ? (
            filter_input(INPUT_COOKIE, $key)
              ?: (is_callable($default) ? call_user_func($default) : $default)
           ) : $_COOKIE;
  }

  /**
   * Returns the current host and port (omitted if port 80 or 443).
   *
   * @return string
   */
  public static function host(){

    $host = $_SERVER['HTTP_X_FORWARDED_HOST']
            ?? $_SERVER['HTTP_HOST']
            ?? $_SERVER['SERVER_NAME']
            ?? $_SERVER['HOSTNAME']
            ?? 'localhost';
    // HTTP_X_FORWARDED_HOST can contain multiple comma-separated proxy hops,
    // we only need the last one
    if (strpos($host,',')!==false) $host = trim(substr(strrchr($host, ','), 1)) ?: $host;

    // Host can contain the port info "host:port"
    $host = explode(':', $host, 2);

    $port = (int)($host[1] ?? $_SERVER['SERVER_PORT'] ?? 80);

    // Hide port if the standard HTTP (80) or HTTPS (443)
    $host = $host[0] . (($port && ($port != 80 && $port != 443)) ? ":$port" : '');

    return static::filterWith('host', $host);
  }

  /**
   * Returns the current protocol.
   *
   * @return string
   */
  public static function protocol(){
    return 'http' . (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS'])!=='off') ? 's' : '');
  }

  /**
   * Returns the current request URL, complete with host and protocol.
   *
   * @return string
   */
  public static function URL(){
    return static::protocol() . '://' . static::host() . static::URI();
  }

  /**
   * Retrive header
   * Returns all headers if you pass `null` as $key
   *
   * @param  string $key The name of the input value
   * @param  mixed $default The value or the value returning function
   *
   * @return Object The returned value or null.
   */
  public static function header($key=null,$default=null){
    $key = 'HTTP_'.strtr(strtoupper($key), '-', '_');
    return $key ? (
            $_SERVER[$key]
              ?? (is_callable($default) ? call_user_func($default) : $default)
           ) : $_SERVER;
  }

  /**
   * Returns the current request URI.
   *
   * @return string
   */
  public static function URI(){
    $serv_uri = $_SERVER['REQUEST_URI']
                ?? $_SERVER['ORIG_PATH_INFO']
                ?? $_SERVER['PATH_INFO']
                ?? '/';
    $uri = rtrim(strtok($serv_uri, '?'), '/') ?: '/';
    return static::filterWith('URI', $uri);
  }

  /**
   * Returns the current base URI (The front-controller directory)
   *
   * @return string
   */
  public static function baseURI(){
    $dir = dirname($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '') ?? '/';
    // In CLI mode, $dir sometimes is equal to current directory : "."
    return  $dir == '.' ? '/' : $dir;
  }

  /**
   * Returns the HTTP Method
   *
   * @return string
   */
  public static function method(){
   return static::filterWith('method', strtolower($_SERVER['REQUEST_METHOD'] ?? 'get'));
  }

  /**
   * Returns the remote IP
   *
   * @return string
   */
  public static function IP(){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
         ?? $_SERVER['HTTP_X_FORWARDED_HOST']
         ?? $_SERVER['REMOTE_ADDR']
         ?? $_SERVER['HTTP_CLIENT_IP']
         ?? '';

    // HTTP_X_FORWARDED_FOR / _HOST can contain multiple comma-separated proxy hops,
    // we only need the last one
    if (strpos($ip,',') !== false) $ip = trim(substr(strrchr($ip, ','), 1)) ?: $ip;

    return static::filterWith('IP', $ip);
  }

  /**
   * Returns the remote UserAgent
   *
   * @return string
   */
  public static function UA(){
   return static::filterWith('UA', strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''));
  }

  /**
   * Returns request body data, convert to object if content type is JSON
   * Gives you all request data if you pass `null` as $key
   *
   * @param  string $key The name of the key requested
   * @param  mixed $default The value or the value returning function
   *
   * @return mixed The request body data
   */
  public static function data($key=null, $default=null){
    // Check if we must retrieve the input data...
    if (null === static::$body){
      // Check if content type is */json
      if ((false !== stripos($_SERVER['HTTP_CONTENT_TYPE'] ?? '','json'))
           || (false !== stripos($_SERVER['CONTENT_TYPE'] ?? '','json'))) {
        // Automatically decode input json
        static::$body = json_decode(file_get_contents("php://input"));
      } else {
       if (empty($_POST)) {
          static::$body = file_get_contents("php://input");
        } else {
          static::$body = (object)$_POST;
        }
      }
    }
    return $key
          ? (static::$body->$key ?? (is_callable($default) ? call_user_func($default) : $default))
          : static::$body;
  }

}
