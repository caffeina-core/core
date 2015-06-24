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

  public static function encode($payload, $secret, $algo = 'HS256') {

    $header         = [
      'typ' => 'JWT',
      'alg' => $algo,
    ];
  
    $segments       = [
      rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'),'='),
      rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'),'='),
    ];
  
    $signing_input  = implode('.', $segments);
  
    $signature      = static::sign($signing_input, $secret, $algo);
    $segments[]     = rtrim(strtr(base64_encode($signature), '+/', '-_'),'=');
    
    return implode('.', $segments);
  
  }

  public static function decode($jwt, $secret = null, $verify = true){
    
    $tokens = explode('.', $jwt);
    if (count($tokens) != 3) throw new \Exception('Token not valid');

    list($headb64, $payloadb64, $cryptob64) = $tokens;
    
    if (null === ($header = json_decode(base64_decode(strtr($headb64, '-_', '+/'))))) 
      throw new \Exception('Invalid encoding');
    
    if (null === ($payload = json_decode(base64_decode(strtr($payloadb64, '-_', '+/'))))) 
      throw new \Exception('Invalid encoding');
    
    $signature = json_decode(base64_decode(strtr($cryptob64, '-_', '+/')));

    if ($verify) {
      if (empty($header->alg)) throw new \Exception('Invalid encoding');

      if ($signature != static::sign("$headb64.$payloadb64", $secret, $header->alg))
        throw new \Exception('Token verification failed');
    }

    return $payload;
  }


  protected static function sign($payload, $secret, $algo = 'HS256') {
    $algos = [
      'HS512' => 'sha512',
      'HS384' => 'sha384',
      'HS256' => 'sha256',
    ];
    if (empty($algos[$algo])) throw new \Exception('Algorithm not supported');
    return hash_hmac($algos[$algo], $payload, $secret, true);
  }

}
