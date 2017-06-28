<?php

namespace Core\HTTP;

class Response {

  protected $headers  = [],
            $contents = '',
            $info     = '';

  public function __construct($contents, array $headers, $info){
    $this->contents = $contents;
    $this->headers  = $headers;
    $this->info     = $info;
  }

  public function __toString(){
    return $this->contents;
  }

  public function value(){
    static $cache = null;
    if (null === $cache) $cache = $this->type() == 'application/json' ? json_decode($this->contents) : $this->contents;
    return $cache;
  }

  public function headers(){
    return $this->headers;
  }

  public function type(){
    static $cache = null;
    if (null === $cache) $cache = trim(strtok($this->info['content_type'] ?? 'application/octet-stream',';'));
    return $cache;
  }

  public function status(){
    return $this->info['http_code'] ?? 0;
  }

  public function info($key=null){
    return null === $key ? $this->info : ($this->info[$key] ?? '');
  }

}