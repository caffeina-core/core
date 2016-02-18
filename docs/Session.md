The [[Session]] module allow you to make hashes of variables.

### Create a new session

Start the session handler with the `Session::start` method.

```php
Session::start();
```

You can pass a custom SID string as parameter.

```php
Session::start("AWESOME_APP_SID");
```

### Close and clear session

All saved data and the session can be deleted with the `Session::clear` method.

```php
Session::clear();
```

### Retrieve a session value

You can retrieve a value from session stash via the `Session::get` method. An optional second parameter can be passed for a default value if the requested one is missing.

```php
$mydata = Session::get('mydata',"some default data");
```

### Set a session value

You can set a value into session stash via the `Session::set` method.

```php
$mydata = Session::get('my_options',[
  'a' => 1,
  'b' => 2,
]);

$mydata['a']++;

print_r( Session::set('my_options',$mydata) );
```

First run

```
Array
(
    [a] => 1
    [b] => 2
)
```

Second run

```
Array
(
    [a] => 2
    [b] => 2
)
```

### Check if a key is in session stash

You can check if a variable is in session stash with the `Session::exists` method.

```php
if(!Session::exists('user')) Redirect::to('/login');
```

