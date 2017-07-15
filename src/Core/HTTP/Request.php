<?php

namespace Core\HTTP;

use Core\URL as URL;

class Request {

  public $method   = 'GET',
         $url      = null,
         $headers  = [],
         $body     = '';

  public function __construct($method, $url, $headers=[], $data = null){
    $this->method   = strtoupper($method);
    $this->url      = new URL($url);
    $this->headers  = (array)$headers;

    if ($data) {
      if (isset($this->headers["Content-Type"]) && $this->headers["Content-Type"]=='application/json')
        $this->body = json_encode($data);
      else
        $this->body = http_build_query($data);
    }

  }

  /**
   * @return string
   */
  public function __toString(){
    return "$this->method {$this->url->path}{$this->url->query} HTTP/1.1\r\n"
          ."Host: {$this->url->host}\r\n"
          .($this->headers ? implode("\r\n",$this->headers) . "\r\n" : '')
          ."\r\n{$this->body}";
  }
}