<?php

class DeferredTest extends PHPUnit_Framework_TestCase {

  public function testDeferred(){
    $self = $this;
    $flag = false;

    call_user_func(function() use (&$flag, &$self){
      $_ = new Deferred(function() use (&$flag){
        $flag = true;
      });
      $self->assertFalse($flag);
    });

    $self->assertTrue($flag);
  }

}

