The [[SQL]] module expose a shorthand for common database methods extending the PDO layer.

### Bind to database
---

You can bind the SQL module to a database with a DSN (Data Source Name) string via the `connect` method.
Connection is lazy-loaded at the first database access.

```php
SQL::connect('mysql:host=localhost;dbname=test','root','password');
```

The event `core.sql.connect` si fired upon database connection.

```php
Event::on('core.sql.connect',function(){
  SQL::exec('SET NAMES "UTF8"');
});
```
### Execute a SQL statement
---

You can execute a SQL statement with the `exec` method. The query will be prepared and you can pass optional binding parameters as last function argument.

```php
SQL::exec('TRUNCATE TABLE `users`');

SQL::exec('DELETE FROM `users` WHERE `age` < 16');
```

### Retrieve a single value
---

The `value` method executes the query, with the optional parameters and returns the first column of the first row of the results.

```php
$total_users = SQL::value('SELECT COUNT(1) FROM `users`');

$user_is_registered = !!SQL::value('SELECT 1 FROM `users` WHERE username = :usr_name',[
  'usr_name' => $username
]);
```


### Retrieve a single row
---

The `single` method executes the query, with the optional parameters and runs the passed callback with the current row object.

```php
SQL::single('SELECT username, points FROM `rankings` LIMIT 1',function($rank){
  echo 'The Winner is : ',$rank->username,' with ',$rank->points,' points!';
});
```
### Retrieve rows
---

The `each` method executes the query, with the optional parameters and runs the passed callback with the current row object for every row of the results.

```php
SQL::each('SELECT * FROM `users`',function($user){
  echo '<li><a href="mailto:', $user->email ,'">', $user->name ,'</a></li>';
});
```

### Retrieve all results
---

The `all` method is used to retrieve all results in a single call.

```php
echo json_encode( SQL::all('SELECT `name` , `email` FROM `users`') );
```


### Insert a new row
---

The `insert` method is used to insert into a defined table a new row, passed as an associative array.

```php
$inserted_item_id = SQL::insert('users',[
  'name'     => 'Stannis Baratheon',
  'password' => 'im_the_one_true_king',
]);
```

### Update a single row
---

The `update` method is used to change a single row data, passed as an associative array.

```php
SQL::update('users',[
  'id'       => 321,
  'name'     => 'King Stannis Baratheon',
]);
```

You can also override the name of the primary key column as the third function parameter, default is `id`

```php
SQL::update('users',[
  'email'    => 'stannis@baratheon.com',
  'name'     => 'King Stannis Baratheon',
],'email');
```

### Delete a single row
---

The `delete` method is used to remove a single row data.

```php
SQL::delete( 'users', [ 321, 432 ] );
```

You can also override the name of the primary key column as the third function parameter, default is `id`

```php
SQL::delete( 'users', [ 'mario@rossi.it', 'sara@rossi.it' ], 'email' );
```

### Debug queries
---

You can bind a function to the `core.sql.query` event for listening every executed query. 

```php
Event::on('core.sql.query',function($query,$params,$statement){
  echo "SQL Query  : $query \n";
  echo "Parameters : ", print_r($params,true), "\n";
  echo "Success    : ", ($statement?'Yes':'No'), "\n";
});
```
