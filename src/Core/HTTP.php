<?php

/**
 * HTTP
 *
 * cURL proxy.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016-2017 - http://caffeina.com
 */

namespace Core;

class HTTP {
  use Module,
      Events;

  protected static $UA          = "Mozilla/4.0 (compatible; Core::HTTP; Windows NT 6.1)",
                   $json_data   = false,
                   $headers     = [],
                   $last_info   = null,
                   $proxy       = null; // host:port

  /**
   * @return \Core\HTTP\Response
   */
  protected static function request (
    $method,
    $url,
    $data=[],
    array $headers=[],
    $data_as_json=false,
    $username = null,
    $password = null
  ){
    $http_method = strtoupper($method);



    // ******* TRANSPORT ******* //
    $ch  = curl_init($url);
    $opt = [
      CURLOPT_CUSTOMREQUEST   => $http_method,
      CURLOPT_SSL_VERIFYHOST  => false,
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_USERAGENT       => static::$UA,
      CURLOPT_HEADER          => false,
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
          $queried_url               .= (false === strpos($queried_url,'?')) ? '?' : '&';
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

    $headers = [];
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers) {
      $len = strlen($header);
      $header = explode(':', $header, 2);
      if (count($header) < 2) // ignore invalid headers
        return $len;

      $name = strtolower(trim($header[0]));
      if (!array_key_exists($name, $headers))
        $headers[$name] = [trim($header[1])];
      else
        $headers[$name][] = trim($header[1]);

      return $len;
    });

    $result = new HTTP\Response(
      curl_exec($ch),
      $headers,
      curl_getinfo($ch)
    );

    curl_close($ch);

    static::trigger("request", $result);
    return $result;
  }

  public static function useJSON($value = null){
    return null === $value ? static::$json_data : static::$json_data = $value;
  }

  /**
   * @return void
   */
  public static function addHeader($name, $value){
    static::$headers[$name] = $value;
  }

  /**
   * @return void
   */
  public static function removeHeader($name){
    unset(static::$headers[$name]);
  }

  public static function headers($name = null){
    return null === $name
           ? static::$headers
           : ( static::$headers[$name] ?? '' );
  }

  public static function userAgent($value = null){
    return null === $value ? static::$UA : static::$UA = $value;
  }

  public static function proxy($value = false){
    return false === $value ? static::$proxy : static::$proxy = $value;
  }

  public static function get($url, $data = null, array $headers=[], $username = null, $password = null){
    return static::request('get',$url,$data,$headers,false,$username,$password);
  }

  public static function post($url, $data = null, array $headers=[], $username = null, $password = null){
    return static::request('post',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function put($url, $data = null, array $headers=[], $username = null, $password = null){
    return static::request('put',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function delete($url, $data = null, array $headers=[], $username = null, $password = null){
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

