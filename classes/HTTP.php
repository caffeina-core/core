<?php

/**
 * HTTP
 *
 * cURL proxy.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class HTTP {
  use Module, Events;

  protected static $UA                    = "Mozilla/4.0 (compatible; Core::HTTP; Windows NT 6.1)",
                   $json_data             = false,
                   $headers               = [],
                   $last_response_header  = null,
                   $last_response_body    = null,
                   $last_info             = null,
                   $proxy                 = null; // host:port

  protected static function request($method, $url, $data=[], array $headers=[], $data_as_json=false, $username=null, $password = null){
    $http_method = strtoupper($method);
    $ch  = curl_init($url);
    $opt = [
      CURLOPT_CUSTOMREQUEST   => $http_method,
      CURLOPT_SSL_VERIFYHOST  => false,
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_USERAGENT       => static::$UA,
      CURLOPT_HEADER          => true,
      CURLOPT_VERBOSE         => true,
      CURLOPT_MAXREDIRS       => 10,
      CURLOPT_FOLLOWLOCATION  => true,
      CURLOPT_ENCODING        => '',
      CURLOPT_PROXY           => static::$proxy,
    ];

    if($username && $password) {
      $opt[CURLOPT_USERPWD] = "$username:$password";
    }

    $headers = array_merge($headers,static::$headers);

    if($http_method == 'GET'){
        if($data && is_array($data)){
          $tmp                       = [];
          $queried_url               = $url;
          foreach($data as $key=>$val) $tmp[] = $key.'='.$val;
          $queried_url               .= (strpos($queried_url,'?') === false) ? '?' : '&';
          $queried_url               .= implode('&',$tmp);
          $opt[CURLOPT_URL]          = $queried_url;
          $opt[CURLOPT_HTTPGET]      = true;
          unset($opt[CURLOPT_CUSTOMREQUEST]);
        }
    } else {
        $opt[CURLOPT_CUSTOMREQUEST]  = $http_method;
        if($data_as_json or is_object($data)){
          $headers['Content-Type']   = 'application/json';
          $opt[CURLOPT_POSTFIELDS]   = json_encode($data);
        } else {
          $opt[CURLOPT_POSTFIELDS]   = http_build_query($data);
        }
    }

    curl_setopt_array($ch,$opt);
    $_harr = [];
    foreach($headers as $key=>$val)  $_harr[] = $key.': '.$val;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $_harr);
    $result = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $contentType = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
    static::$last_response_header = substr($result, 0, $header_size);
    $result = substr($result, $header_size);
    static::$last_info = curl_getinfo($ch);
    if(false !== strpos($contentType,'json')) $result = json_decode($result);
    curl_close($ch);
    static::trigger("request", $result, static::$last_info);
    static::$last_response_body = $result;
    return $result;
  }

  public static function useJSON($value=null){
    return $value===null ? static::$json_data : static::$json_data = $value;
  }

  protected static function trasformRawHeaders($headers) {
    foreach (explode("\r\n", trim($headers)) as $line) {
      if (empty($line)) continue;
      $splitted = explode(':', $line);
      $res[isset($splitted[1])? trim($splitted[0]) : 'extra'][] = trim(end($splitted));
    }
    return $res;
  }

  public static function lastResponseBody(){
    return static::$last_response_body;
  }

  public static function lastResponseHeader(){
    if (static::$last_response_header && !is_array(static::$last_response_header)) {
      static::$last_response_header = static::trasformRawHeaders(static::$last_response_header);
    }
    return static::$last_response_header;
  }

  public static function addHeader($name,$value){
    static::$headers[$name] = $value;
  }

  public static function removeHeader($name){
    unset(static::$headers[$name]);
  }

  public static function headers($name=null){
    // null === $name ?? static::$headers ?? static::$headers[$name]
    return null === $name
           ? static::$headers
           : ( isset(static::$headers[$name]) ? static::$headers[$name] : '' );
  }

  public static function userAgent($value=null){
    return $value===null ? static::$UA : static::$UA = $value;
  }

  public static function proxy($value=false){
    return $value===false ? static::$proxy : static::$proxy = $value;
  }

  public static function get($url, $data=null, array $headers=[], $username = null, $password = null){
    return static::request('get',$url,$data,$headers,false,$username,$password);
  }

  public static function post($url, $data=null, array $headers=[], $username = null, $password = null){
    return static::request('post',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function put($url, $data=null, array $headers=[], $username = null, $password = null){
    return static::request('put',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function delete($url, $data=null, array $headers=[], $username = null, $password = null){
    return static::request('delete',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function info($url = null){
    if ($url){
      curl_setopt_array($ch = curl_init($url), [
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_CONNECTTIMEOUT  => 10,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_USERAGENT       => static::$UA,
        CURLOPT_HEADER          => false,
        CURLOPT_ENCODING        => '',
        CURLOPT_FILETIME        => true,
        CURLOPT_NOBODY          => true,
        CURLOPT_PROXY           => static::$proxy,
      ]);
      curl_exec($ch);
      $info = curl_getinfo($ch);
      curl_close($ch);
      return $info;
    } else {
      return static::$last_info;
    }
  }

}

class HTTP_Request {
  public $method   = 'GET',
         $url      = null,
         $headers  = [],
         $body     = '';

  public function __construct($method, $url, $headers=[], $data=null){
    $this->method   = strtoupper($method);
    $this->url      = new URL($this->url);
    $this->headers  = (array)$headers;
    if ($data) {
      if (isset($this->headers["Content-Type"]) && $this->headers["Content-Type"]=='application/json')
        $this->body = json_encode($data);
      else
        $this->body = http_build_query($data);
    }
  }

  public function __toString(){
    return "$this->method {$this->url->path}{$this->url->query} HTTP/1.1\r\n"
          ."Host: {$this->url->host}\r\n"
          .($this->headers ? implode("\r\n",$this->headers) . "\r\n" : '')
          ."\r\n{$this->body}";
  }
}


class HTTP_Response {
  public $status   = 200,
         $headers  = [],
         $contents = '';

  public function __construct($contents, $status, $headers){
    $this->status   = $status;
    $this->contents = $contents;
    $this->headers  = (array)$headers;
  }

  public function __toString(){
    return $this->contents;
  }
}

