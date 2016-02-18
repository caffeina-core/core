The [[Persistence]] module allow you to augment a class with a database persistence layer.

### Standard persistence
---

```php

class User {
  // Add persistence feat
  use Persistence;

  public $id, $email, $name;

}

// Enable persistence defining the target database table
User::persistOn('users',[
  'key' => 'id'  // The primary key, 'id' is the default.
]);

$me = new User();
$me->id = 1000;
$me->email = 'john@email.com';
$me->name = 'John Appleseed';

// Save data on database
$me->save();

// Retrieve an user by it's primary key
$current_user = User::load(1000);
echo $current_user->email;
```

```
john@email.com
```

### Override Save/Load callbacks
---

You can override the save/load callback with custom methods.

```php
// Persist on files instead of Database

User::onLoad(function($id, $table, $options){
  $keyname = $options['key'];
  $path = '/data/' . $table . '/' . Hash::md5($id) . '.php';
  $obj = null;
  if (file_exists($path)) $obj = unserialize(file_get_contents($path));
  return $obj;
});

User::onSave(function($table, $options){
  // The current object is binded on $this variable
  $keyname = $options['key'];
  $path = '/data/' . $table . '/' . Hash::md5( $this->$keyname ) . '.php';
  return file_put_contents(serialize($this)) > 0;
});
