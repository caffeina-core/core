<?php


class EmailTest extends PHPUnit_Framework_TestCase {

  public function testEmptyEnvelope(){
    $empty_mail = new \Email\Envelope();
    $this->assertNotNull($empty_mail);
    $this->assertNotFalse(strpos($empty_mail->build(), 'Content-Transfer-Encoding: quoted-printable'));
  }

  public function testSetterGetters(){
    $mail = new \Email\Envelope();
    $this->assertNotNull($mail);

    foreach(['to','cc','bcc'] as $key){
      $mail->$key('stefano@test.com');
      $this->assertEquals(implode('|',$mail->$key()),'stefano@test.com',"SetterGetter : $key");
      $mail->$key('clara@test.com');
      $this->assertEquals(implode('|',$mail->$key()),'stefano@test.com|clara@test.com',"SetterGetter : $key");
      $mail->$key(false);
      $this->assertEmpty(implode('|',$mail->$key()),"SetterGetter : $key");
    }

    foreach(['from','replyTo','subject','contentType','message'] as $key){
      $mail->$key('stefano@test.com');
      $this->assertEquals($mail->$key(),'stefano@test.com',"SetterGetter : $key");
      $mail->$key('clara@test.com');
      $this->assertEquals($mail->$key(),'clara@test.com',"SetterGetter : $key");
      $mail->$key(false);
      $this->assertEmpty($mail->$key(),"SetterGetter : $key");
    }

  }

  public function testStandardEnvelope(){
    $mail = new \Email\Envelope([
      'to'          => [                                // You can pass an array
        'Stefano <stefano@caffeina.com>',
        'test@email.it',
      ],
      'from'        => "tester@php.test",               // A single address
      'bcc'         => "bcc1@test.com,bcc2@test.com",   // or a comma separated list
      'subject'     => "HELLOSUBJECT",
      'message'     => "HELLOBODY",
    ]);

    $this->assertNotNull($mail);
    $email_src = $mail->build();
    $this->assertNotFalse(strpos($email_src, "Subject: HELLOSUBJECT\r\n"),"SUBJECT not found");
    $this->assertNotFalse(strpos($email_src, "\r\n\r\nHELLOBODY\r\n\r\n"),"BODY not found");
    $this->assertNotFalse(strpos($email_src, "To: Stefano <stefano@caffeina.com>, test@email.it\r\n"),"TO not found");
    $this->assertNotFalse(strpos($email_src, "From: tester@php.test\r\n"),"FROM not found");
    $this->assertNotFalse(strpos($email_src, "Bcc: bcc1@test.com, bcc2@test.com\r\n"),"BCC not found");

    $mail->contentType('text/plain');
    $email_src = $mail->build();
    $this->assertNotFalse(strpos($email_src, "Content-Type: text/plain\r\n"),"CONTENT-TYPE failed to change");
  }


  public function testAttachments(){
    $mail = new \Email\Envelope();
    $this->assertNotNull($mail);

    $mail->attach(__FILE__);

    $this->assertNotFalse(strpos($mail->build(), "Content-Disposition: attachment; filename=\"".basename(__FILE__)."\"\r\n"),"DIRECT URL FILE attachment failed");

    $mail->attach([
      'name'    => 'test.fake.txt',
      'content' => 'ATTACHME!',
    ]);

    $this->assertNotFalse(strpos($mail->build(),
           "Content-Disposition: attachment; filename=\"test.fake.txt\"\r\n"),
    "INDIRECT FILE Name attachment failed");

    $this->assertNotFalse(strpos($mail->build(),
           "\r\nQVRUQUNITUUh\r\n\r\n"),
    "INDIRECT FILE Content attachment failed");

  }

  public function testUnknownEmailDriver(){
    try {
      Email::using("fak3_driver");
    } catch (Exception $e) {
      return true;
    }
    $this->fail("Expecting exception, fak3_driver is fake.");
  }

  public function testEmailFacade(){
    $self = $this;
    Email::using('proxy',[
      'hook' => 'test.proxy',
    ]);

    $success = true;
    Event::on('test.proxy',function($envelope) use ($self, &$success) {
      $success = count($envelope->to()) == 3;
    });

    Email::send([
      'to' => 'me,you,other',
    ]);

    if (!$success) $this->fail("Email.Send(array) failed.");

    Event::off('test.proxy');

    $success = true;
    Event::on('test.proxy',function($envelope) use ($self, &$success) {
      $success = $envelope->from() == 'test';
    });

    $mail = Email::create([
      'from' => 'test',
    ]);

    Email::send($mail);

    if (!$success) $this->fail("Email.Send(envelope) failed.");

  }

}



