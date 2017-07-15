<?php

/**
 * Email\Envelope
 *
 * Wraps and compile a MIME Email envelope.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core\Email;

class Envelope {

  protected  $uid,
             $to,
             $from,
             $cc,
             $bcc,
             $replyTo,
             $subject,
             $message,
             $contentType = 'text/html; charset="utf-8"',
             $attachments,
             $compiled_head,
             $compiled_body;

  public function __construct($email=null){
    if ($email) {
      $email = (object)$email;
      if (isset($email->to))           $this->to($email->to);
      if (isset($email->from))         $this->from($email->from);
      if (isset($email->cc))           $this->cc($email->cc);
      if (isset($email->bcc))          $this->bcc($email->bcc);
      if (isset($email->replyTo))      $this->replyTo($email->replyTo);
      if (isset($email->subject))      $this->subject($email->subject);
      if (isset($email->message))      $this->message($email->message);
      if (isset($email->attachments))  $this->attach($email->attachments);
    }
    $this->uid  = '_CORE_'.md5(uniqid(time()));

  }

  /**
   * @return void
   */
  protected function add_emails(&$pool, $emails, $append=true){
    $this->compiled_head = null;
    foreach ((array)$emails as $values) {
      foreach(preg_split('/\s*,\s*/',$values) as $value) {
        if (strpos($value,'<')!==false){
          $value   = str_replace('>','',$value);
          $parts   = explode('<',$value,2);
          $name    = trim(current($parts));
          $email   = trim(end($parts));
          $address = "$name <{$email}>";
        } else {
          $address = $value;
        }
        if ($append) $pool[] = $address; else $pool = $address;
      }
    }
  }

  public function from($value=null){
    if ($value!==null && $value) {
      $this->add_emails($this->from, $value, false);
    } else if ($value===false) $this->from = '';
    return $this->from;
  }

  public function to($value=null){
    if ($value!==null && $value) {
      $this->add_emails($this->to, $value);
    } else if ($value===false) $this->to = [];
    return $this->to;
  }

  public function cc($value=null){
    if ($value!==null && $value) {
      $this->add_emails($this->cc, $value);
    } else if ($value===false) $this->cc = [];
    return $this->cc;
  }

  public function bcc($value=null){
    if ($value!==null && $value) {
      $this->add_emails($this->bcc, $value);
    } else if ($value===false) $this->bcc = [];
    return $this->bcc;
  }

  public function replyTo($value=null){
    if ($value!==null && $value) {
      $this->add_emails($this->replyTo, $value, false);
    } else if ($value===false) $this->replyTo = '';
    return $this->replyTo;
  }

  public function subject($value=null){
    if ($value!==null && $value) {
      $this->compiled_head = null;
      $this->subject = $value;
    } else if ($value===false) $this->subject = '';
    return $this->subject;
  }

  public function contentType($value=null){
    if ($value!==null && $value) {
      if (empty($this->attachments)) $this->compiled_head = null;
      $this->compiled_body = null;
      $this->contentType = $value;
    } else if ($value===false) $this->contentType = '';
    return $this->contentType;
  }

  public function message($value=null){
    if ($value!==null && $value) {
      $this->compiled_body = null;
      $this->message = $value;
    } else if ($value===false) $this->message = '';
    return $this->message;
  }

  /**
   * @return void
   */
  public function attach($file){
    $this->compiled_body = null;
    if (isset($file->content) || isset($file['content'])) {
      $this->attachments[] = $file;
    } else foreach ((array)$file as $curfile) {
      $this->attachments[] = $curfile;
    }
  }

  public function attachments($file=null){
    if ($file!==null && $file) $this->attach($file);
    return $this->attachments ?: [];
  }

  public function head($recompile = false){
    if ($recompile || (null === $this->compiled_head)){
      $head   = [];
      $head[] = "Subject: {$this->subject}";
      if ($this->from)                        $head[] = "From: {$this->from}";
      if (is_array($this->to)  && !empty($this->to))  $head[] = "To: "  . implode(', ',$this->to);
      if (is_array($this->cc)  && !empty($this->cc))  $head[] = "Cc: "  . implode(', ',$this->cc);
      if (is_array($this->bcc) && !empty($this->bcc)) $head[] = "Bcc: " . implode(', ',$this->bcc);
      if ($this->replyTo)                     $head[] = "Reply-To: {$this->replyTo}";
      $head[] = "Content-Type: multipart/mixed; boundary=\"{$this->uid}\"";
      $head[] = 'MIME-Version: 1.0';
      $head[] = '';
      $this->compiled_head = implode("\r\n", $head);
    }
    return \Core\Email::filterWith('source.head', $this->compiled_head);
  }

  public function body($recompile = false){
    if ($recompile || (null === $this->compiled_body)){
      $body   = [];
      $body[] = "--{$this->uid}";
      $body[] = "Content-Type: {$this->contentType}";
      $body[] = "Content-Transfer-Encoding: quoted-printable";
      $body[] = '';
      $body[] = quoted_printable_encode($this->message);
      $body[] = '';

      if (!empty($this->attachments)) foreach ((array)$this->attachments as $file) {

        if (is_string($file)) {
          $name = basename($file);
          $data = file_get_contents($file);
        } else {
          $name = isset($file['name'])    ? $file['name']    : 'untitled';
          $data = isset($file['content']) ? $file['content'] : '';
        }

        $body[] = "--{$this->uid}";
        $body[] = "Content-Type: application/octet-stream; name=\"{$name}\"";
        $body[] = "Content-Transfer-Encoding: base64";
        $body[] = "Content-Disposition: attachment; filename=\"{$name}\"";
        $body[] = '';
        $body[] = chunk_split(base64_encode($data));
        $body[] = '';
      }

      $body[] = "--{$this->uid}";

      $this->compiled_body = implode("\r\n", $body);
    }
    return \Core\Email::filterWith('source.body', $this->compiled_body);
  }

  public function build(){
    return \Core\Email::filterWith('source', $this->head() . "\r\n" . $this->body() );
  }

}
