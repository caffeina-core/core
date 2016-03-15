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

  protected $recipients  = [];
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
    $head = $body = [];

    if($this->from)     $head[] = 'From: ' . $this->from;
    if($this->replyTo)  $head[] = 'Reply-To: ' . $this->replyTo;

    $head[] = 'MIME-Version: 1.0';
    $head[] = "Content-Type: multipart/mixed; boundary=\"".$uid."\"";

    $body[] = "--$uid";
    $body[] = "Content-Type: text/html; charset=UTF-8";
    $body[] = "Content-Transfer-Encoding: quoted-printable";
    $body[] = '';
    $body[] = quoted_printable_encode($this->message);
    $body[] = '';


    foreach ($this->attachments as $file) {
      if (is_string($file)) {
        $name = basename($file);
        $data = file_get_contents($file);
      } else {
        $name = $file['name'];
        $data = $file['content'];
      }

      $body[] = "--$uid";
      $body[] = "Content-Type: application/octet-stream; name=\"".$name."\"";
      $body[] = "Content-Transfer-Encoding: base64";
      $body[] = "Content-Disposition: attachment; filename=\"".$name."\"";
      $body[] = '';
      $body[] = chunk_split(base64_encode($data));
      $body[] = '';
    }

    $body[] = "--$uid";

    $success = true;
    $head    = implode("\r\n",$head);
    $body    = implode("\r\n",$body);

    foreach ($this->recipients as $to) {
      $current_success = mail(
           $to,
           $this->subject,
           $body,
           $head
      );
      \Event::trigger('core.email.send',$to,$this->from,$this->subject,$body,$success);
      $success = $success && $current_success;
    }

    $this->recipients  = [];
    $this->attachments = [];
    $this->from        = '';
    $this->replyTo     = '';
    $this->subject     = '';
    $this->message     = '';

    return $success;
  }

}

