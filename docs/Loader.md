The [[Loader]] module allow you easy enable class autoloading.

### Register the class loader
---

The Loader module automatically register itself by simply including the `Loader.php` file.

If you installed core via composer it's already registered upon `vendor/autoload.php` inclusion.


### Add a class path
---

The simplest way to retrieve a value from cache is via the `get` method.

```php
Loader::addPath('/path/to/my/classes');
```
