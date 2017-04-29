<?php

use Core\{
  Enum
};

class Days extends Enum {
  const MONDAY    = 1,
        TUESDAY   = 2,
        WEDNESDAY = 3,
        THURSDAY  = 4,
        FRIDAY    = 5,
        SATURDAY  = 6,
        SUNDAY    = 7;
}

class AIStatus extends Enum {
  const IDLE        = 'idle',
        PATROL      = 'ptrl',
        SCOUTING    = 'scou',
        DISTRACTED  = 'dist',
        FOCUSED     = 'focs',
        ATTACKING   = 'attk',
        DEFENDING   = 'defn',
        FLEEING     = 'flee',
        RESTING     = 'rest',
        UNCONSCIOUS = 'unks',
        FRIENDLY    = 'frnd',
        HOSTILE     = 'host';
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

