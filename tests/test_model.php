<?php

SQL::exec("
    CREATE TABLE `books` (
        id integer primary key,
        title text
    );
");

class Book extends Model {
    const _PRIMARY_KEY_ = 'books.id';
}

// Create
$b1 = Book::create([
    'id'     => 1,
    'title'  => 'My book',
]);

test(SQL::value('select title from books where id=1') == 'My book','Model','Create');

// Save
$b1->title = "My Awesome Book";
$b1->save();

test(SQL::value('select title from books where id=1') == 'My Awesome Book','Model','Save');

// Load
$b2 = Book::create([
    'id'     => 2,
    'title'  => 'Necronomicon',
]);

$b2_loaded = Book::load(2);
test($b2_loaded && $b2_loaded->title == 'Necronomicon','Model','Load');

// All
test(json_encode(Book::all()) == '[{"id":"1","title":"My Awesome Book"},{"id":"2","title":"Necronomicon"}]','Model','All');

// Where
test(json_encode(Book::where('id=2')) == '[{"id":"2","title":"Necronomicon"}]','Model','Where');
