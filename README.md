# Core
Caffeina PHP SDK

---

### Installation


Add package to your **composer.json**:

```
{
  "require": {
    "caffeina-core/core": "*"
  }
}
```

Run composer:

```
php composer.phar install -o
```

Now the entire toolchain is already available upon the vendor autoloader inclusion.

```
<?php
// Load vendors
include 'vendors/autoload.php';

Route::on('/',function(){
	echo "Hello from Core!";
});

// Dispatch route
Route::dispatch();

// Send response to the browser
Response::send();
```