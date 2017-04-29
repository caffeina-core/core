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

namespace Core;

class Token {

  public static function encode($payload, $secret, $algo = 'HS256') {
    $encoded_payload = implode('.', [rtrim(strtr(base64_encode(json_encode([
        'typ' => 'JWT',
        'alg' => $algo,
      ])), '+/', '-_'),'='),
      rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'),'='),
    ]);
    return $encoded_payload . '.' . static::sign($encoded_payload, $secret, $algo);
  }

  public static function decode($jwt, $secret = null, $verify = true){

    if (substr_count($jwt,'.') != 2) throw new \Exception('Token not valid');

    list($encoded_header, $encoded_payload, $client_sig) = explode('.', $jwt);

    if (null === ($payload = json_decode(base64_decode(strtr($encoded_payload, '-_', '+/')))))
      throw new \Exception('Invalid encoding');


    if ($verify) {
      if (null === ($header = json_decode(base64_decode(strtr($encoded_header, '-_', '+/')))))
        throw new \Exception('Invalid encoding');

      if (empty($header->alg)) throw new \Exception('Invalid encoding');

      if ($client_sig != static::sign("$encoded_header.$encoded_payload", $secret, $header->alg))
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
    if (empty($algos[$algo])) throw new \Exception('Signing algorithm not supported');
    return rtrim(strtr(base64_encode(hash_hmac($algos[$algo], $payload, $secret, true)), '+/', '-_'),'=');
  }

}
