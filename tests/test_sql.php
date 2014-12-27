<?php

//Event::on('core.sql.query',function($sql){ echo "SQL: $sql\n"; });

test(SQL::exec("
    CREATE TABLE `users` (
        id integer primary key,
        email text,
        password text
    );
"),'SQL','Exec');


$id1 = SQL::insert('users',[
    'email' => 'user@email.com',
    'password' => '1111',
]);

$id2 = SQL::insert('users',[
    'email' => 'frank@email.com',
    'password' => '2222',
]);

$id3 = SQL::insert('users',[
    'email' => 'frank@email.com',
    'password' => '3333',
]);

$id4 = SQL::insert('users',[
    'email' => 'frank@email.com',
    'password' => '4444',
]);


test(($id1 == 1) && ($id2 == 2),'SQL','Insert primary key passing');


$cc = 0;
SQL::each('SELECT id FROM users',function($row) use (&$cc) {
    $cc += $row->id;
});

test($cc == 10,'SQL','Each, row callback.');
test(json_encode(SQL::each('SELECT id FROM users')) == '[{"id":"1"},{"id":"2"},{"id":"3"},{"id":"4"}]','SQL','Each, retrieving all.');

test(SQL::update('users',[
    'id'       => 2,
    'password' => 'prova',
]),'SQL','Update');


test(SQL::value('SELECT password FROM users WHERE id=?',[2]) == "prova",'SQL','Value');


$iou = SQL::insertOrUpdate('users',[
    'id'       => "2",
    'password' => '2002',
]);
test($iou && (SQL::value('SELECT password FROM users WHERE id=?',[2]) == 2002),'SQL','Insert or Update');


test(SQL::delete('users',2),'SQL','Delete single');
test(SQL::delete('users',[1,4]),'SQL','Delete multiple');

test(SQL::delete('users') && (SQL::value("SELECT count(*) FROM users")==0),'SQL','Delete all');
