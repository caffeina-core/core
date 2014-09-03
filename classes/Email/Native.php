<?php

/**
 * Email\Native
 *
 * Email\Native PHP mail() driver.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */


namespace Email;

class Native implements \EmailInterface {
  
  protected $recipients = [];
  protected $attachments = [];
  protected $from;
  protected $replyTo;
  protected $subject;
  protected $message;

  public function addAddress($email,$name=''){
    $this->recipients[] = empty($name)?$email:"$name <{$email}>";
  }

  public function from($email,$name=''){
    $this->from = empty($name)?$email:"$name <{$email}>";
  }

  public function replyTo($email,$name=''){
    $this->replyTo = empty($name)?$email:"$name <{$email}>";
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

  public function send(){
    $uid = md5(uniqid(time()));
    $headers = [];

    if($this->from)     $headers[] = 'From: '.$this->from;
    if($this->replyTo)  $headers[] = 'Reply-To: '.$this->replyTo;

    $headers[] = 'MIME-Version: 1.0';
    $headers[] = "Content-Type: multipart/mixed; boundary=\"".$uid."\"";
    $headers[] = "This is a multi-part message in MIME format.";
    $headers[] = "--$uid";
    $headers[] = "Content-type: text/html; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: quoted-printable";
    $headers[] = '';
    $headers[] = quoted_printable_encode($this->message);
    $headers[] = '';
    
    
    foreach ($this->attachments as $file) {
      $headers[] = "--$uid";
      $headers[] = "Content-type: application/octet-stream; name=\"".basename($file)."\"";
      $headers[] = "Content-Transfer-Encoding: 7bit";
      $headers[] = '';
      $headers[] = chunk_split(base64_encode(file_get_contents($file)));
      $headers[] = '';
    }

    $headers[] = "--$uid--";

    $success = true;
    foreach ($this->recipients as $to) {
      $success = mail(
           $to,
           $this->subject,
           '',
           implode("\r\n",$headers)
      ) && $success;
    }
    return $success;
  }

}

