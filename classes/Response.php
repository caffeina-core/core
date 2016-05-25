<?php

/**
 * Response
 *
 * Handles the HTTP Response for the current execution.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Response {
    use Module;

    const TYPE_JSON               = 'application/json',
          TYPE_HTML               = 'text/html',
          TYPE_TEXT               = 'text/plain',
          TYPE_CSS                = 'text/css',
          TYPE_XML                = 'text/xml',
          TYPE_SVG                = 'image/svg+xml',
          TYPE_JS                 = 'application/javascript',
          TYPE_BIN                = 'application/octet-stream';

    protected static $payload     = [],
                     $status      = 200,
                     $charset     = "utf-8",
                     $headers     = ['Content-Type' => ['text/html; charset=utf-8']],
                     $buffer      = null,
                     $force_dl    = false,
                     $link        = null,
                     $sent        = false;


    public static function charset($charset){
        static::$charset = $charset;
    }

    public static function type($mime){
        static::header('Content-Type',$mime . (static::$charset ? '; charset='.static::$charset : ''));
    }

    /**
     * Force download of Response body
     * @param  string/bool $filename Pass a falsy value to disable download or pass a filename for exporting content
     * @return [type]        [description]
     */
    public static function download($filename){
        static::$force_dl = $filename;
    }

    /**
     * Start capturing output
     */
    public static function start(){
        static::$buffer = ob_start();
    }

    /**
     * Enable CORS HTTP headers.
     */
    public static function enableCORS(){

        // Allow from any origin
        if ($origin = filter_input(INPUT_SERVER,'HTTP_ORIGIN')) {
          static::header('Access-Control-Allow-Origin', $origin);
          static::header('Access-Control-Allow-Credentials', 'true');
          static::header('Access-Control-Max-Age', 86400);
        }

        // Access-Control headers are received during OPTIONS requests
        if (filter_input(INPUT_SERVER,'REQUEST_METHOD') == 'OPTIONS') {
            static::clean();

            if (filter_input(INPUT_SERVER,'HTTP_ACCESS_CONTROL_REQUEST_METHOD')) {
              static::header('Access-Control-Allow-Methods',
                'GET, POST, PUT, DELETE, OPTIONS, HEAD, CONNECT, PATCH, TRACE');
            }
            if ($req_h = filter_input(INPUT_SERVER,'HTTP_ACCESS_CONTROL_REQUEST_HEADERS')) {
              static::header('Access-Control-Allow-Headers',$req_h);
            }

            static::send();
            exit;
        }
    }

    public static function sent() {
        return static::$sent;
    }

    /**
     * Finish the output buffer capturing.
     * @return string The captured buffer
     */
    public static function end(){
        if (static::$buffer){
            static::$payload[] = ob_get_contents();
            ob_end_clean();
            static::$buffer = null;
            return end(static::$payload);
        }
    }

    /**
     * Check if an response output buffering is active.
     * @return boolean
     */
    public static function isBuffering(){
        return static::$buffer;
    }

    /**
     * Clear the response body
     */
    public static function clean(){
        static::$payload = [];
    }

    /**
     * Append a JSON object to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function json($payload){
        static::type(static::TYPE_JSON);
        static::$payload[] = json_encode($payload, Options::get('core.response.json_flags',JSON_NUMERIC_CHECK));
    }

    /**
     * Append a text to the buffer.
     * @param  mixed $payload Text to append to the response buffer
     */
    public static function text(){
        static::type(static::TYPE_TEXT);
        static::$payload[] = implode('',func_get_args());
    }

    /**
     * Append an XML string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function xml(){
        static::type(static::TYPE_XML);
        static::$payload[] = implode('',func_get_args());
    }

    /**
     * Append a SVG string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function svg(){
        static::type(static::TYPE_SVG);
        static::$payload[] = implode('',func_get_args());
    }

    /**
     * Append an HTML string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function html(){
        static::type(static::TYPE_HTML);
        static::$payload[] = implode('',func_get_args());
    }

    /**
     * Append data to the buffer.
     *  Rules :
     *  - Callables will be called and their results added (recursive)
     *  - Views will be rendered
     *  - Objects, arrays and bools will be JSON encoded
     *  - Strings and numbers will be appendend to the response
     *
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function add(){
      foreach(func_get_args() as $data){
        switch (true) {
          case is_callable($data) :
            return static::add($data());
          case is_a($data, 'View') :
            return static::$payload[] = "$data";
          case is_object($data) || is_array($data) || is_bool($data):
            return static::json($data);
          default:
            return static::$payload[] = $data;
        }
      }
    }

    public static function status($code,$message=''){
        static::header('Status',$message?:$code,$code);
    }

    public static function header($name,$value,$code=null){
        static::$headers[$name] = [$value,$code];
    }

    public static function error($code=500,$message='Application Error'){
        Event::trigger('core.response.error',$code,$message);
        static::status($code,$message);
    }

    public static function body($setBody=null){
      if ($setBody) static::$payload = [$setBody];
      return Filter::with('core.response.body',
                is_array(static::$payload) ? implode('',static::$payload) : static::$payload
             );
    }

    public static function headers($setHeaders=null){
       if ($setHeaders) static::$headers = $setHeaders;
       return static::$headers;
    }

    /**
     * Save response as an object, for serialization or cache storage
     *
     * @method save
     *
     * @return array Headers and body of the response
     */
    public static function save(){
        return [
          'head' => static::$headers,
          'body' => static::body(),
        ];
    }

    /**
     * Load response from a saved state
     *
     * @method load
     *
     * @param  array $data head/body saved state
     */
    public static function load($data){
      $data = (object)$data;
      if (isset($data->head)) static::headers($data->head);
      if (isset($data->body)) static::body($data->body);
    }

    public static function send($force = false){
      if (!static::$sent || $force) {
        static::$sent = true;
        Event::trigger('core.response.send');
        if (false === headers_sent()) foreach (static::$headers as $name => $value_code) {

            if (is_array($value_code)) {
                list($value, $code) = (count($value_code) > 1) ? $value_code : [current($value_code), 200];
            } else {
                $value = $value_code;
                $code  = null;
            }

            if ($value == 'Status'){
              if (function_exists('http_response_code')){
                http_response_code($code);
              } else {
                header("Status: $code", true, $code);
              }

            } else {
                $code
                ? header("$name: $value", true, $code)
                : header("$name: $value", true);
            }
        }
        if (static::$force_dl) header('Content-Disposition: attachment; filename="'.static::$force_dl.'"');
        echo static::body();
      }
    }

}
