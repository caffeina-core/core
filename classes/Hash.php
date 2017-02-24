<?php

/**
 * Hash
 *
 * Hashing shorthands.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Hash {
	use Module;

	/**
   * Create ah hash for payload
   * @param  mixed $payload    The payload string/object/array
   * @param  integer $method   The hashing method, default is "md5"
   * @param  bool $raw_output  When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
   * @return string            The hash string
   */
	public static function make($payload, $method = 'md5', $raw_output = false) {
		return $method == 'murmur' ? static::murmur(serialize($payload)) : hash($method, serialize($payload), $raw_output);
	}

	/**
	 * Verify if given payload matches hash
	 * @param  mixed $payload  The payload string/object/array
	 * @param  string $hash    The hash string
	 * @param  integer $method The hashing method
	 * @return bool            Returns `true` if payload matches hash
	 */
	public static function verify($payload, $hash, $method = 'md5') {
		return static::make($payload, $method) == $hash;
	}

	/**
	 * List registered hashing algorithms
	 *
	 * @method methods
	 *
	 * @return array   Array containing the list of supported hashing algorithms.
	 */
	public static function methods() {
    // Merge PHP provided algos with ours (murmur)
		return array_merge(hash_algos(), ['murmur','murmurhash3']);
	}

	/**
	 * Check if an alghoritm is registered in current PHP
	 *
	 * @method can
	 *
	 * @param  string $algo The hashing algorithm name
	 *
	 * @return bool
	 */
	public static function can($algo) {
    // Faster than : in_array(explode(',',implode(',',static::methods())))
		return strpos(implode(',',static::methods()).',', "$algo,") !== false;
	}

	/**
	 * Static magic for creating hashes with a specified algorithm.
	 *
	 * See [hash-algos](http://php.net/manual/it/function.hash-algos.php) for a list of algorithms
	 */
	public static function __callStatic($method, $params) {
		return self::make(current($params), $method);
	}

	public static function uuid($type = 4, $namespace = '', $name = '') {
		switch ($type) {
		case 3:if (preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
				'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/Si', $namespace) !== 1) {
				return false;
			}

			$nhex = str_replace(array('-', '{', '}'), '', $namespace);
			$nstr = '';for ($i = 0; $i < strlen($nhex); $i += 2) {
				$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
			}

			$hash = md5($nstr . $name);
			return sprintf('%08s-%04s-%04x-%04x-%12s',
				substr($hash, 0, 8), substr($hash, 8, 4),
				(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
				(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
				substr($hash, 20, 12));
		case 5:if (preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
				'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/Si', $namespace) !== 1) {
				return false;
			}

			$nhex = str_replace(array('-', '{', '}'), '', $namespace);
			$nstr = '';for ($i = 0; $i < strlen($nhex); $i += 2) {
				$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
			}

			$hash = sha1($nstr . $name);
			return sprintf('%08s-%04s-%04x-%04x-%12s',
				substr($hash, 0, 8), substr($hash, 8, 4),
				(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
				(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
				substr($hash, 20, 12));
		default:case 4:return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
				mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
		}
	}

  public static function murmur($key, $seed = 0, $as_integer=false) {
    $key  = array_values(unpack('C*',(string) $key));
    $klen = count($key);
    $h1   = (int)$seed;
    $i    = $remainder = 0;
    for ($bytes=$klen-($remainder=$klen&3) ; $i<$bytes ; ) {
      $k1 = $key[$i]
        | ($key[++$i] << 8)
        | ($key[++$i] << 16)
        | ($key[++$i] << 24);
      ++$i;
      $k1  = (((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16))) & 0xffffffff;
      $k1  = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
      $k1  = (((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16))) & 0xffffffff;
      $h1 ^= $k1;
      $h1  = $h1 << 13 | ($h1 >= 0 ? $h1 >> 19 : (($h1 & 0x7fffffff) >> 19) | 0x1000);
      $h1b = (((($h1 & 0xffff) * 5) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 5) & 0xffff) << 16))) & 0xffffffff;
      $h1  = ((($h1b & 0xffff) + 0x6b64) + ((((($h1b >= 0 ? $h1b >> 16 : (($h1b & 0x7fffffff) >> 16) | 0x8000)) + 0xe654) & 0xffff) << 16));
    }
    $k1 = 0;
    switch ($remainder) {
      case 3: $k1 ^= $key[$i + 2] << 16;
      case 2: $k1 ^= $key[$i + 1] << 8;
      case 1: $k1 ^= $key[$i];
      $k1  = ((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16)) & 0xffffffff;
      $k1  = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
      $k1  = ((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16)) & 0xffffffff;
      $h1 ^= $k1;
    }
    $h1 ^= $klen;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
    $h1  = ((($h1 & 0xffff) * 0x85ebca6b) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 13 : (($h1 & 0x7fffffff) >> 13) | 0x40000);
    $h1  = (((($h1 & 0xffff) * 0xc2b2ae35) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);

		return $as_integer ? $h1 : base_convert($h1 ,10, 32);
	}

  public static function random($bytes=9){
    return strtr(base64_encode(static::random_bytes($bytes)),'+/=','-_');
  }

  public static function random_bytes($bytes){
    static $randf = null;
    if (function_exists('random_bytes')) {
      return \random_bytes($bytes);
    } else if (function_exists('mcrypt_create_iv')) {
      return @\mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
    } else {
      if (null === $randf) {
        if ($randf = fopen('/dev/urandom', 'rb')) {
          $st = fstat($randf);
          function_exists('stream_set_read_buffer')
            && stream_set_read_buffer($randf, 8);
          function_exists('stream_set_chunk_size')
            && stream_set_chunk_size($randf, 8);
          if (($st['mode'] & 0170000) !== 020000) {
            fclose($randf);
            $randf = false;
          }
        }
      }
      if ($randf) {
        $remaining = $bytes;
        $buf = '';
        do {
          $read = fread($randf, $remaining);
          if ($read === false) {
            $buf = false;
            break;
          }
          $remaining -= strlen($read);
          $buf .= $read;
        } while ($remaining > 0);
        if ($buf !== false) {
          if (strlen($buf) === $bytes) {
            return $buf;
          }
        }
      }
    }
  }

}
