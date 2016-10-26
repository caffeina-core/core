<?php

/**
 * Email\SMTP
 *
 * Email\SMTP PHP driver.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace Email;

class Smtp implements Driver {

  protected
    $socket,
    $host,
    $secure,
    $port,
    $lastCode,
    $lastMessage,
    $username,
    $password;

  public function onInit($options) {
    $options        = (object)$options;
    $this->host     = isset($options->host)     ? $options->host     : 'localhost';
    $this->username = isset($options->username) ? $options->username : false;
    $this->secure   = isset($options->secure)   ? $options->secure   : !empty($this->username);
    $this->port     = isset($options->port)     ? $options->port     : ($this->secure ? 465 : 25);
    $this->password = isset($options->password) ? $options->password : false;
  }

  protected function connect(){
    if ($this->socket) $this->close();
    $url = ($this->secure ? 'tls' : 'tcp') ."://{$this->host}";
    $this->socket = fsockopen( $url, $this->port, $errno, $errstr, 30 );
    if (!$this->socket) throw new \Exception("Unable to connect to $url on port {$this->port}.");
    $this->lastMessage = '';
    $this->lastCode = 0;
  }

  public function close(){
    $this->socket && @fclose($this->socket);
  }

  protected function write($data, $nl = 1){
    $payload = $data . str_repeat("\r\n",$nl);
    fwrite($this->socket, $payload);
    \Email::trigger("smtp.console",$payload);
  }

  protected function expectCode($code){

    $this->lastMessage = '';
    while (substr($this->lastMessage, 3, 1) != ' '){
      $this->lastMessage = fgets($this->socket, 256);
    }
    $this->lastCode = 1 * substr($this->lastMessage, 0, 3);
    \Email::trigger("smtp.console",$this->lastMessage);
    if ($code != $this->lastCode) {
      throw new \Exception("Expected $code returned {$this->lastMessage}");
    }
    return true;
  }

  protected function cleanAddr($email){
    return preg_replace('((.*?)<([\w.@-]+)>(.*?))','$2',$email);
  }

  protected function SMTPmail($from,$to,$body){
    try {
    $this->connect();
    $this->expectCode(220);

    $this->write("EHLO {$this->host}");
    $this->expectCode(250);

    if ($this->username){
      $this->write("AUTH LOGIN");
      $this->expectCode(334);
      $this->write(base64_encode($this->username));
      $this->expectCode(334);
      $this->write(base64_encode($this->password));
      $this->expectCode(235);
    }

    $from = $this->cleanAddr($from);

    $this->write("MAIL FROM: <{$from}>");
    $this->expectCode(250);

    $to = $this->cleanAddr($to);

    $this->write("RCPT TO: <{$to}>");
    $this->expectCode(250);

    $this->write("DATA");
    $this->expectCode(354);

    $this->write($body);

    $this->write(".");
    $this->expectCode(250);

    $this->write("QUIT");

    $this->close();
    } catch (\Exception $e) {
      \Email::trigger('error',$e->getMessage());
      return false;
    }
    return true;
  }

  public function onSend(Envelope $envelope){
    $results = [];
    foreach ($envelope->to() as $to) {
      $results[$to] = $this->SMTPmail($envelope->from(), $to, $envelope->build());
    }
    return $results;
  }

}

