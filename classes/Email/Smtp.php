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
    $recipients = [],
    $attachments = [],
    $from,
    $replyTo,
    $subject,
    $message,
    $socket,
    $host,
    $secure,
    $port,
    $lastCode,
    $lastMessage,
    $username,
    $password;

  public function __construct($options = null) {
    $options        = (object)$options;
    $this->host     = isset($options->host)     ? $options->host     : 'localhost';
    $this->username = isset($options->username) ? $options->username : false;
    $this->secure   = isset($options->secure)   ? $options->secure   : ($this->username ? true : false);
    $this->port     = isset($options->port)     ? $options->port     : ($this->secure ? 465 : 25);
    $this->password = isset($options->password) ? $options->password : false;
  }

  protected function connect(){
    if ($this->socket) $this->close();
    $url = ($this->secure ? 'tls' : 'tcp') ."://{$this->host}";
    $this->socket = fsockopen( $url, $this->port, $errno, $errstr, 30 );
    if ( ! $this->socket ) throw new \Exception("Unable to connect to $url on port {$this->port}.");
    $this->lastMessage = '';
    $this->lastCode = 0;
  }

  public function close(){
    $this->socket && @fclose($this->socket);
  }

  protected function write($data, $nl = 1){
    $payload = $data . str_repeat("\r\n",$nl);
    fwrite($this->socket, $payload);
  }

  protected function expectCode($code){

    $this->lastMessage = '';
    while (substr($this->lastMessage, 3, 1) != ' '){
      $this->lastMessage = fgets($this->socket, 256);
    }

    $this->lastCode = 1 * substr($this->lastMessage, 0, 3);
    return $code == $this->lastCode;
  }

  public function addAddress($email,$name=''){
    if (!$email) return;
    if (empty($name) ) $name = strtok($email,'@');
    $this->recipients[] = (object)['name'=>$name,'email'=>$email,'full'=>"$name <{$email}>"];
  }

  public function from($email,$name=''){
    if (!$email) return;
    if (empty($name) ) $name = strtok($email,'@');
    $this->from = (object)['name'=>$name,'email'=>$email,'full'=>"$name <{$email}>"];
  }

  public function replyTo($email,$name=''){
    if (!$email) return;
    if (empty($name) ) $name = strtok($email,'@');
    $this->replyTo = (object)['name'=>$name,'email'=>$email,'full'=>"$name <{$email}>"];
  }

  public function subject($text){
    $this->subject = $text;
  }

  public function message($text){
    $this->message = $text;
  }

  public function addAttachment($file){
    $this->attachments[] = $file;
  }

  protected function SMTPmail($to,$subject,$body,$heads=''){
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
      $this->expectCode(334);
    }

    $this->write("MAIL FROM: <{$this->from->email}>");
    $this->expectCode(250);

    $this->write("RCPT TO: <{$to}>");
    $this->expectCode(250);

    $this->write("DATA");
    $this->expectCode(354);

    $this->write("Subject: {$subject}");

    $this->write($heads);
    $this->write($body);

    $this->write(".");
    $success = $this->expectCode(250);

    $this->write("QUIT");

    $this->close();
    return $success;
  }


  public function send(){
    $uid = '_CORE_'.md5(uniqid(time()));
    $headers = [];

    if($this->from)     $headers[] = 'From: '.$this->from->full;
    if($this->replyTo)  $headers[] = 'Reply-To: '.$this->replyTo->full;

    $headers[] = 'MIME-Version: 1.0';
    $headers[] = "Content-Type: multipart/mixed; boundary=\"".$uid."\"";
    $headers[] = "";
    $headers[] = "--$uid";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: quoted-printable";
    $headers[] = '';
    $headers[] = quoted_printable_encode($this->message);
    $headers[] = '';


    foreach ($this->attachments as $file) {

      if (is_string($file)) {
        $name = basename($file);
        $data = file_get_contents($file);
      } else {
        $name = $file['name'];
        $data = $file['content'];
      }

      $headers[] = "--$uid";
      $headers[] = "Content-Type: application/octet-stream; name=\"".$name."\"";
      $headers[] = "Content-Transfer-Encoding: base64";
      $headers[] = "Content-Disposition: attachment; filename=\"".$name."\"";
      $headers[] = '';
      $headers[] = chunk_split(base64_encode($data));
      $headers[] = '';
    }

    $headers[] = "--$uid--";

    $success = true;

    $body = implode("\r\n",$headers);

    foreach ($this->recipients as $to) {

      $current_success = $this->SMTPmail(
           $to->email,
           $this->subject,
           '',
           $body
      );

      \Event::trigger('core.email.send',$to->full,$this->from->full,$this->subject,$body,$success);
      $success = $success && $current_success;
    }
    return $success;
  }

}

