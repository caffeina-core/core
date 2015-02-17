<?php

/**
 * Email\Mandrill
 *
 * Email\Mandrill API Driver.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0.0
 * @copyright Caffeina srl - 2015 - http://caffeina.co
 */


namespace Email;

class Mandrill implements Driver {
  
  protected 
    $payload = [
      "key"     => '',
      "message" => [
        "html"                      => '',
        //"text"                      => '',
        "subject"                   => '',
        "from_email"                => '',
        "from_name"                 => '',
        "to"                        => [],
        "headers"                   => [],
        "async"                     => true,
        "attachments"               => [],
        "images"                    => [],
        "important"                 => false,
        "auto_text"                 => true,
        "inline_css"                => false,
        "preserve_recipients"       => false,
        "track_opens"               => null,
        "track_clicks"              => null,
        "auto_html"                 => null,
        "url_strip_qs"              => null,
        //"view_content_link"         => null,
        //"bcc_address"               => false,
        //"tracking_domain"           => null,
        //"signing_domain"            => null,
        //"return_path_domain"        => null,
        //"merge"                     => false,
        //"merge_language"            => 'handlebars',
        //"global_merge_vars"         => [],
        //"merge_vars"                => [],
        //"tags"                      => [],
        //"send_at"                   => '',
        //"subaccount"                => '',
        //"google_analytics_domains"  => [],
        //"google_analytics_campaign" => '',
        //"metadata"                  => [],
        //"recipient_metadata"        => [],
      ], 
    ],
    $attachments;
  
  public function __construct($options){
    $this->payload["key"] = empty($options["key"]) ? \Options::get('mail.mandrill.key','') : $options["key"];
    if (empty($this->payload["key"])) throw new \Exception("Email::Mandrill needs an active API key.");
  }

  public function addAddress($email,$name=''){
    $this->payload["message"]["to"][] = [
      "email" => $email,
      "name"  => $name,
      "type"  => 'to',
    ];
  }

  public function from($email,$name=''){
    $this->payload["message"]["from_email"] = $email;
    $this->payload["message"]["from_name"] = $name;
  }

  public function replyTo($email,$name=''){
    $this->payload["message"]["headers"]["Reply-To"] = $email;
  }

  public function subject($text){
    $this->payload["message"]["subject"] = $text;
  }

  public function message($text){
    $this->payload["message"]["html"] = $text;
  }

  public function addAttachment($file){
    $this->attachments[] = [
      'name'    => basename($file),
      'mime'    => trim(exec('file -bi '.escapeshellarg($file))),
      'content' => base64_encode(file_get_contents($file)),
    ];
  }

  public function send(){

    if (empty($this->payload["key"])) throw new \Exception("Email::Mandrill needs an active API key.");

    $old_usejson = \HTTP::useJSON();
    \HTTP::useJSON(true);
    
    $success = \HTTP::post('https://mandrillapp.com/api/1.0/messages/send.json',(object)$this->payload,[
      'User-Agent' => 'Mandrill-Curl/1.0',
    ]);

    \HTTP::useJSON($old_usejson);
     
    \Event::trigger('core.email.send',
        $this->payload["message"]["to"],
        $this->payload["message"]["from_email"],
        $this->payload["message"]["subject"],
        json_encode($this->payload), $success);

    return $success;
  }

}

