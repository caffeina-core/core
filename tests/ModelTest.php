<?php

class Book extends Model {
    const _PRIMARY_KEY_ = 'books.id';
}

class ModelTest extends PHPUnit_Framework_TestCase {
		private $b1,$b2;

		public function __construct(){
     	SQL::connect('sqlite::memory:');
			SQL::exec("CREATE TABLE books (
			  id integer primary key,
			  title text
			);");

			$this->b1 = Book::create([
			    'id'     => 1,
			    'title'  => 'My book',
			]);

			$this->b2 = Book::create([
			    'id'     => 2,
			    'title'  => 'Necronomicon',
			]);
		}

    public function testCreate(){
				$results = SQL::value('SELECT title from books where id=1');
				$this->assertEquals('My book',$results);
    }

    public function testSave(){
				$this->b1->title = "My Awesome Book";
				$this->b1->save();
				$results = SQL::value('SELECT title from books where id=1');
				$this->assertEquals('My Awesome Book',$results);
    }

    public function testLoad(){
				$b2_loaded = Book::load(2);

        $this->assertNotNull($b2_loaded);
				$this->assertNotFalse($b2_loaded);
				$this->assertEquals('Necronomicon',$b2_loaded->title);

    }

    public function testRetrieveAll(){
				$results = Book::all();
				$this->assertEquals(2,count($results));
    }

    public function testWhereSimple(){
				$results = json_encode(Book::where('id=2'));
				$this->assertEquals('[{"id":"2","title":"Necronomicon"}]',$results);
    }

}

