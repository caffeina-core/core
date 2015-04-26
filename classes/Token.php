<?php

/**
 * Token
 * 
 * A JWT implementation
 * http://openid.net/specs/draft-jones-json-web-token-07.html
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Token {
  use Module;

  protected static $options = array(
    'secret'          => null,
    'signing_method'  => 'sha256',
    'verify'          => true, // flag to enable/disable signature verification
  );

  public static function init(array $options){
    foreach($options as $key => $val){
      static::$options[$key] = $val;
    }
    if (!static::$options['secret']) {
      throw new Exception( 'You must provide a secret passfrase.' );
    }
  }

  public static function secret($secret){ 
    static::$options['secret'] = $secret;
  }

  public static function parse($payload){
    $packet     = static::decode($payload);
    $data       = $packet['d'];
    $signature  = $packet['s'];
    if( !static::$options['verify'] || $signature === static::sign($data) ){
      return $data;
    } else {
      throw new Exception( 'Invalid payload signature or corrupted data.' );
    }
  }

  public static function pack($data){
    return static::encode(array(
      'd' => $data,
      's' => static::$options['verify'] ? static::sign($data) : false,
    ));
  }

  protected static function sign($data){
    return hash_hmac(static::$options['signing_method'],serialize($data),static::$options['secret']);
  }

  /**
   * Encodes a string as a URL-safe Base64
   * @param $data The string to encode
   * @return string The encoded string
   */
  protected static function encode($data){
    return rtrim(strtr(base64_encode(addslashes(json_encode($data))), '+/', '-_'),'=');
  }

  /**
   * Decodes a string from URL-safe Base64
   * @param $data The string to decode
   * @return string The decoded string
   */
  protected static function decode($data){
    return json_decode(stripslashes(base64_decode(strtr($data, '-_', '+/'))));
  }

}

Token::secret(md5(__FILE__));
