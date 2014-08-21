<?php

/**
 * HTTP
 *
 * cURL proxy.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class HTTP {
  protected static $UA = "Mozilla/4.0 (compatible; Core::HTTP; Windows NT 6.1)";
  protected static $json_data = false;
  protected static $headers = [];

  protected static function request($method,$url,$data=null,array $headers=[], $data_as_json=false, $username = null, $password = null){
    $http_method = strtoupper($method);
    $ch = curl_init($url);
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
    ];

    if($username && $password) {
      $opt[CURLOPT_USERPWD]   =  "$username:$password";
      $opt[CURLOPT_HTTPAUTH]  =  CURLAUTH_BASIC;
    }

    $headers = array_merge($headers,static::$headers);

    if($http_method=='GET'){
      if($data && is_array($data)){
        $tmp = [];
        $queried_url = $url;
        foreach($data as $key=>$val) $tmp[] = $key.'='.$val;
        $queried_url .= (strpos($queried_url,'?')===false)?'?':'&';
        $queried_url .= implode('&',$tmp);
        $opt[CURLOPT_URL] = $queried_url;
      } 
    } else {
      if($http_method=='POST') {
        $opt[CURLOPT_POST]            = true;
      } else {
        $opt[CURLOPT_CUSTOMREQUEST]   = $http_method;
      }
      if(null!==$data){
        if($data_as_json){
          $headers['Content-Type']    = 'application/json';
          $opt[CURLOPT_POSTFIELDS]    = json_encode($data);     
        } else {
          $opt[CURLOPT_POSTFIELDS]    = http_build_query($data);
        }
      }
    }
    curl_setopt_array($ch,$opt);
    foreach($headers as $key=>$val) curl_setopt($ch, CURLOPT_HTTPHEADER, $key.': '.$val);
    $result = curl_exec($ch);
    $contentType = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

    if(strpos($contentType,'application/json') === 0) $result = json_decode($result);
    curl_close($ch);
    return $result;
  }

  public static function useJSON($value=null){
    return static::$json_data = ($value===null?static::$json_data:$value);
  }

  public static function addHeader($name,$value){
    static::$headers[$name] = $value;
  }

  public static function removeHeader($name){
    unset(static::$headers[$name]);
  }

  public static function headers($name=null){
    return null===$name?static::$headers:(isset(static::$headers[$name])?static::$headers[$name]:'');
  }

  public static function userAgent($value=null){
    return static::$UA = ($value===null?static::$UA:$value);
  }

  public static function get($url,$data=null,array $headers=[], $username = null, $password = null){
    return static::request('get',$url,$data,$headers,false,$username,$password);
  }

  public static function post($url,$data=null,array $headers=[], $username = null, $password = null){
    return static::request('post',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function put($url,$data=null,array $headers=[], $username = null, $password = null){
    return static::request('put',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function delete($url,$data=null,array $headers=[], $username = null, $password = null){
    return static::request('delete',$url,$data,$headers,static::$json_data,$username,$password);
  }

  public static function info($url,array $options=[]){
    $ch = curl_init($url);
    curl_setopt_array($ch, array_merge([
      CURLOPT_CUSTOMREQUEST   => $method,
      CURLOPT_SSL_VERIFYHOST  => false,
      CURLOPT_CONNECTTIMEOUT  => 10,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_USERAGENT       => static::$UA,
      CURLOPT_HEADER          => false,
      CURLOPT_MAXREDIRS       => 10,
      CURLOPT_FOLLOWLOCATION  => true,
      CURLOPT_ENCODING        => '',
      CURLOPT_FILETIME        => true,
      CURLOPT_NOBODY          => true,
    ],$options));

    curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return $info;
  }

}
