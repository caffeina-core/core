<?php

/**
 * Email\Native
 *
 * Email\Native PHP mail() driver.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace Email;

class Native implements Driver {
  
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
      if (is_string($file)) {
        $name = basename($file);
        $data = file_get_contents($file);
      } else {
        $name = $file['name'];
        $data = $file['content'];
      }

      $headers[] = "--$uid";
      $headers[] = "Content-type: application/octet-stream; name=\"".$name."\"";
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
      $current_success = mail(
           $to,
           $this->subject,
           '',
           $body
      );
      \Event::trigger('core.email.send',$to,$this->from,$this->subject,$body,$success);
      $success = $success && $current_success;
    }
    return $success;
  }

}

