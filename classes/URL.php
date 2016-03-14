<?php

/**
 * URL
 *
 * Helper object for handling URLs
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

class URL {

  public    $scheme    = false,
            $user      = false,
            $pass      = false,
            $host      = false,
            $port      = false,
            $path      = false,
            $query     = [],
            $fragment  = false;

  public function __construct($url=''){
    if (empty($url) || !is_string($url)) return;
    $tmp_url      = (strpos($url, '://') === false) ? "..N..://$url" : $url;
    if (mb_detect_encoding($tmp_url, 'UTF-8', true) || ($parsed = parse_url($tmp_url)) === false) {
      preg_match('(^((?P<scheme>[^:/?#]+):(//))?((\\3|//)?(?:(?P<user>[^:]+):(?P<pass>[^@]+)@)?(?P<host>[^/?:#]*))(:(?P<port>\\d+))?(?P<path>[^?#]*)(\\?(?P<query>[^#]*))?(#(?P<fragment>.*))?)u', $tmp_url, $parsed);
    }
    foreach($parsed as $k => $v) if(isset($this->$k)) $this->$k = $v;
    if ($this->scheme == '..N..') $this->scheme = null;
    if (!empty($this->query)) {
      parse_str($this->query, $this->query);
    }
  }

  public function __toString(){
    $d = [];
    if ($this->scheme)     $d[] = "{$this->scheme}://";
    if ($this->user)       $d[] = "{$this->user}" . (empty($this->pass)?'':":{$this->pass}") . "@";
    if ($this->host)       $d[] = "{$this->host}";
    if ($this->port)       $d[] = ":{$this->port}";
    if ($this->path)       $d[] = "/" . ltrim($this->path,"/");
    if ($this->query)      $d[] = "?" . http_build_query($this->query);
    if ($this->fragment)   $d[] = "#{$this->fragment}";
    return implode('', $d);
  }

} /* End of class */
