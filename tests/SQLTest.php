<?php

class SQLTest extends PHPUnit_Framework_TestCase {
	public function __construct() {
		SQL::register('database_a', 'sqlite::memory:');
		SQL::register('database_b', 'sqlite::memory:');
	}

	public function testCreateTable() {
		$results = SQL::exec("CREATE TABLE users (
          id integer primary key,
          email text,
          password text
        );");
		$this->assertNotFalse($results);

		$results = SQL::using('database_a')->exec("CREATE TABLE users (
          id integer primary key,
          email text,
          password text
        );");
		$this->assertNotFalse($results);

		$results = SQL::using('database_b')->exec("CREATE TABLE users (
          id integer primary key,
          email text,
          password text
        );");
		$this->assertNotFalse($results);

		$database_b = SQL::using('database_b');

		$database_b->insert('users', [
			'email' => 'test@other.com',
			'password' => 'kek',
		]);

		$this->assertEquals($database_b->value("SELECT password FROM users"), "kek");

	}

	public function testInsert() {
		$id1 = SQL::insert('users', [
			'email' => 'user@email.com',
			'password' => '1111',
		]);

		$id2 = SQL::insert('users', [
			'email' => 'frank@email.com',
			'password' => '2222',
		]);

		$id3 = SQL::insert('users', [
			'email' => 'frank@email.com',
			'password' => '3333',
		]);

		$id4 = SQL::insert('users', [
			'email' => 'frank@email.com',
			'password' => '4444',
		]);

		$this->assertTrue(($id1 == 1) && ($id4 == 4));

	}

	public function testEachRowCallback() {
		$cc = 0;
		SQL::each('SELECT id FROM users', function ($row) use (&$cc) {
			$cc += $row->id;
		});
		$this->assertEquals(10, $cc);
	}

  public function testReduceCallback() {
    $val = SQL::reduce('SELECT id FROM users', function ($cc, $row) {
      $cc += $row->id;
      return $cc;
    }, 0);
    $this->assertEquals(10, $val);

    $val = SQL::reduce('SELECT id FROM users', function ($cc, $row) {
      $row->test = $row->id;
      $cc[] = $row;
      return $cc;
    }, []);
    $espect = '[{"id":"1","test":"1"},{"id":"2","test":"2"},{"id":"3","test":"3"},{"id":"4","test":"4"}]';
    $this->assertEquals($espect, json_encode($val));
  }

  public function testColumn() {
    $ids = SQL::column('SELECT id FROM users',[],0);
    $this->assertEquals('1,2,3,4', implode(',',$ids), "Numeric Column");

    $ids = SQL::column('SELECT id FROM users',[],'id');
    $this->assertEquals('1,2,3,4', implode(',',$ids), "Label Column");
  }

	public function testEachRetrievingAll() {
		$results = SQL::each('SELECT id FROM users');
		$espect = '[{"id":"1"},{"id":"2"},{"id":"3"},{"id":"4"}]';
		$this->assertEquals($espect, json_encode($results));
	}

	public function testUpdate() {
		$results = SQL::update('users', [
			'id' => 2,
			'password' => 'test',
		]);
		$this->assertTrue($results);
	}

	public function testGetValue() {
		$results = SQL::value('SELECT password FROM users WHERE id=?', [2]);
		$this->assertEquals("test", $results);
	}

  public function testGetSingleRow() {
    $results = SQL::single('SELECT password FROM users WHERE id=?', [2]);
    $this->assertEquals("test", $results->password);

    $results = SQL::single('SELECT password FROM users WHERE id=?', [2], function($value) {
      return strtoupper($value->password);
    });
    $this->assertEquals("TEST", $results);
  }

	public function testInsertOrUpdate() {
		$iou = SQL::insertOrUpdate('users', [
			'id' => "2",
			'password' => '2002',
		]);
		$check = SQL::value('SELECT password FROM users WHERE id=?', [2]);
		$this->assertNotFalse($iou);
		$check = SQL::value('SELECT password FROM users WHERE id=?', [2]);
		$this->assertEquals(2002, $check);
	}

	public function testDeleteSingle() {
		$this->assertNotFalse(SQL::delete('users', 2));
		$this->assertEquals("1,3,4", SQL::value("SELECT GROUP_CONCAT(id) FROM users"));
	}

	public function testDeleteMultiple() {
		$this->assertNotFalse(SQL::delete('users', [1, 4]));
		$this->assertEquals("3", SQL::value("SELECT GROUP_CONCAT(id) FROM users"));
	}

	public function testDeleteAll() {
		$this->assertNotFalse(SQL::delete('users'));
		$this->assertEquals(0, SQL::value("SELECT count(*) FROM users"));
	}
/**/
}
