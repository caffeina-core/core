<?php

class Extra extends Model {
  public $id,
         $data;
}

/*******************************************/

class Author extends Model {
  const _PRIMARY_KEY_ = 'authors.id';
  public $id,
         $name;
}

/*******************************************/

class Book extends Model {
  const _PRIMARY_KEY_ = 'books.id';
  public $id,
         $title,
         $author_id,
         $extra_id;
}

Book::hasOne('Author:author_id');
Book::hasOne('Extra:extra_id');

/*******************************************/

Author::hasMany('Book.author_id');

/*******************************************/

class ModelTest extends PHPUnit_Framework_TestCase {
  private $b1, $b2;

  public function __construct() {
    SQL::connect('sqlite::memory:');

    SQL::on('error',function($e,$q){
      echo "SQL\\Error: $e\n\t$q\n";
    });

    SQL::exec("CREATE TABLE extras (
        id integer primary key,
        data text
      );");

    Extra::create([
      'id' => 1,
      'data' => 'Lorem Ipsum Dolor Sit Amet',
    ]);

    SQL::exec("CREATE TABLE authors (
        id integer primary key,
        name text
      );");

    $this->a1 = Author::create([
      'id' => 1,
      'name' => 'Abdul Alhazred',
    ]);

    $this->a2 = Author::create([
      'id' => 2,
      'name' => 'Steven King',
    ]);

    SQL::exec("CREATE TABLE books (
        id integer primary key,
        title text,
        author_id integer,
        extra_id integer
      );");

    $this->b1 = Book::create([
      'id' => 1,
      'title' => 'No, not the aliens this time.',
      'author_id' => 2,
    ]);

    $this->b2 = Book::create([
      'id' => 2,
      'author_id' => 1,
      'extra_id' => 1,
      'title' => 'Necronomicon',
    ]);

  }

  public function testCreate() {
    $results = SQL::value('SELECT title from books where id=1');
    $this->assertEquals($this->b1->title, $results);
  }

  public function testSave() {
    $this->b1->title = "My Awesome Book";
    $this->b1->save();
    $results = SQL::value('SELECT title from books where id=1');
    $this->assertEquals('My Awesome Book', $results);
  }

  public function testLoad() {
    $b2_loaded = Book::load(2);
    $this->assertNotNull($b2_loaded);
    $this->assertNotFalse($b2_loaded);
    $this->assertEquals('Necronomicon', $b2_loaded->title);
  }

  public function testRetrieveAll() {
    $results = Book::all();
    $this->assertEquals(2, count($results));
  }

  public function testCountAll() {
    $results = Book::count();
    $this->assertEquals(2, $results);
  }

  public function testCountWithCondition() {
    $results = Book::count("id=:id",["id" => 2]);
    $this->assertEquals(1, $results);
  }

  public function testWhereSimple() {
    $results = Book::where('id=2');
    $this->assertNotEmpty($results);
    $this->assertEquals(2, $results[0]->id);
  }

  public function testExporter() {
    $this->assertEquals('{"ID":"2"}', json_encode(
      Book::load(2)->export(function($k,$v){
        return $k == 'id' ? [strtoupper($k)=>$v] : false;
      })
    ));
  }

  public function testRelations() {
    $book = Book::load(1);
    $this->assertNotEmpty($book);
    $author = $book->author;
    $this->assertNotEmpty($author);
    $this->assertEquals(2, $author->id);
    $this->assertTrue(is_array($author->books));
    $this->assertTrue(is_a($author->books[0],'Book'));

    //echo "\n",json_encode($book, JSON_PRETTY_PRINT),"\n";

    $author->name = "Lord Soth";
    $author->save();

    //echo "\n",json_encode(Book::load(1), JSON_PRETTY_PRINT),"\n";
    //echo "\n",json_encode(Author::load(2), JSON_PRETTY_PRINT),"\n";

    //die;

  }

}
