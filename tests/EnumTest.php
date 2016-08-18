<?php

class Days extends Enum {
  const MONDAY    = 1;
  const TUESDAY   = 2;
  const WEDNESDAY = 3;
  const THURSDAY  = 4;
  const FRIDAY    = 5;
  const SATURDAY  = 6;
  const SUNDAY    = 7;
}

class AIStatus extends Enum {
  const IDLE        = 'idle';
  const PATROL      = 'ptrl';
  const SCOUTING    = 'scou';
  const DISTRACTED  = 'dist';
  const FOCUSED     = 'focs';
  const ATTACKING   = 'attk';
  const DEFENDING   = 'defn';
  const FLEEING     = 'flee';
  const RESTING     = 'rest';
  const UNCONSCIOUS = 'unks';
  const FRIENDLY    = 'frnd';
  const HOSTILE     = 'host';
}

class EnumTest extends PHPUnit_Framework_TestCase {

  public function testEnumCantInstantiate() {
    $this->assertFalse(is_callable("AIStatus::__construct"),'An Enum cannot be instanciated');
  }

  public function testEnumKey() {
    $this->assertEquals("MONDAY", Days::key(1),          'key 1');
    $this->assertFalse(Days::key(21) ,                   'key 21');
    $this->assertEquals("PATROL", AIStatus::key('ptrl'), 'key ptrl');

    $this->assertFalse(AIStatus::key(0),     'key zero');
    $this->assertFalse(AIStatus::key(''),    'key empty string');
    $this->assertFalse(AIStatus::key(false), 'key false');
    $this->assertFalse(AIStatus::key(true),  'key true');
  }

  public function testEnumHas() {
    $this->assertTrue(Days::has('Sunday'),        'has sunday');
    $this->assertTrue(AIStatus::has('resting'),   'has resting');
  }

  public function testEnumAccess() {
    $this->assertEquals(5, Days::FRIDAY);
    $this->assertEquals('frnd', AIStatus::FRIENDLY);
  }

}

