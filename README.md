# Core
Caffeina PHP SDK

---

### Installation


Add package to your **composer.json**:

```
{
  "require": {
    "caffeina-core/core": "dev-master"
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
include 'vendor/autoload.php';

Route::on('/',function(){
	echo "Hello from Core!";
});

// Dispatch route
Route::dispatch();

// Send response to the browser
Response::send();
```
