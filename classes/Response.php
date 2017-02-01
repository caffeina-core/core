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
    use Module, Events;

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
                     $sent        = false,
                     $links       = [];


    public static function charset($charset){
        static::$charset = $charset;
    }

    public static function type($mime){
        static::header('Content-Type',$mime . (static::$charset ? '; charset='.static::$charset : ''));
    }

    /**
     * Force download of Response body
     * @param  mixed $data Pass a falsy value to disable download, pass a filename for exporting content or array with raw string data
     * @return void
     */
    public static function download($data){
        if (is_array($data)) {
            if (isset($data['filename'])) static::$force_dl = $data['filename'];
            if (isset($data['charset'])) static::charset($data['charset']);
            if (isset($data['mime'])) static::type($data['mime']);
            if (isset($data['body'])) static::body($data['body']);
        } else static::$force_dl = $data;
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
    public static function enableCORS($origin='*'){

        // Allow from any origin
        if ($origin = $origin ?:( isset($_SERVER['HTTP_ORIGIN'])
                    ? $_SERVER['HTTP_ORIGIN']
                    : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '*')
        )) {
          static::header('Access-Control-Allow-Origin',      $origin);
          static::header('Access-Control-Allow-Credentials', 'true');
          static::header('Access-Control-Max-Age',           86400);
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

            self::trigger('cors.preflight');
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
        static::$headers = [];
    }

    /**
     * Append a JSON object to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function json($payload){
        static::type(static::TYPE_JSON);
        static::$payload[] = json_encode($payload, Options::get('core.response.json_flags',JSON_NUMERIC_CHECK|JSON_BIGINT_AS_STRING));
    }

    /**
     * Append a text to the buffer.
     * @param  mixed $payload Text to append to the response buffer
     */
    public static function text(...$args){
        static::type(static::TYPE_TEXT);
        static::$payload[] = implode('',$args);
    }

    /**
     * Append an XML string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function xml(...$args){
        static::type(static::TYPE_XML);
        static::$payload[] = implode('', $args);
    }

    /**
     * Append a SVG string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function svg(...$args){
        static::type(static::TYPE_SVG);
        static::$payload[] = implode('', $args);
    }

    /**
     * Append an HTML string to the buffer.
     * @param  mixed $payload Data to append to the response buffer
     */
    public static function html(...$args){
        static::type(static::TYPE_HTML);
        static::$payload[] = implode('', $args);
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
      if (empty(static::$headers[$name])){
        static::$headers[$name] = [[$value,$code]];
      } else {
        static::$headers[$name][] = [$value,$code];
      }
    }

    public static function error($code=500,$message='Application Error'){
      static::trigger('error',$code,$message);
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
          'head'  => static::$headers,
          'body'  => static::body(),
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
        static::trigger('send');
        Event::trigger('core.response.send');
        if (false === headers_sent()) foreach (static::$headers as $name => $family)
          foreach ($family as $value_code) {

            if (is_array($value_code)) {
                list($value, $code) = (count($value_code) > 1) ? $value_code : [current($value_code), 200];
            } else {
                $value = $value_code;
                $code  = null;
            }

            switch($value){
              case "Status":
                if (function_exists('http_response_code')){
                  http_response_code($code);
                } else {
                  header("Status: $code", true, $code);
                }
              break;
              case "Link":
                  header("Link: $value", false);
              break;
              default:
                if ($code) {
                  header("$name: $value", true, $code);
                } else {
                  header("$name: $value", true);
                }
              break;
            }
        }
        if (static::$force_dl) header('Content-Disposition: attachment; filename="'.static::$force_dl.'"');
        echo static::body();
        static::trigger('sent');
      }
    }


    /**
     * Push resources to client (HTTP/2 spec)
     * @param  string/array $links The link(s) to the resources to push.
     * @return Response     The Route object
     */
    public static function push($links, $type='text'){
      if (is_array($links)){
        foreach($links as $_type => $link) {
            // Extract URL basename extension (query-safe version)
            if (is_numeric($_type)) switch(strtolower(substr(strrchr(strtok(basename($link),'?'),'.'),1))) {
                case 'js': $_type = 'script'; break;
                case 'css': $_type = 'style'; break;
                case 'png': case 'svg': case 'gif': case 'jpg': $_type = 'image'; break;
                case 'woff': case 'woff2': case 'ttf': case 'eof': $_type = 'font'; break;
                default: $_type = 'text'; break;
            }
            foreach ((array)$link as $link_val) {
              static::header("Link","<$link_val>; rel=preload; as=$_type");
            }
        }
      } else {
        static::header("Link","<".((string)$links).">; rel=preload; as=$type");
      }
    }

}
