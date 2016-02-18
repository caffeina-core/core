
Handles the HTTP [[request]] for the current execution.

### Getting an input parameter
---

Inputs passed to the request can be retrieved with the `Request::input($key=null, $default=null)` method.

The function searches for an input named `$key` in the `$_REQUEST` superglobal, if not found returns the `$default` value passed (resolved if `$default` is callable).

If you call `Request::input()` it will returns an associative array of all `$_REQUEST` content.

`$_GET`, `$_POST`, `$_FILES`, `$_COOKIE` can be accessed directly with the `Request::get/post/files/cookie` methods.
 
```php
echo "Hello, ", Request::input('name','Friend'), '!';
```

```
GET /?name=Alyx
```
```
Hello, Alyx!
```

### Getting the URL / URI
---

The `Request::URL()` method returns the current request URL, complete with host and protocol.

The `Request::URI()` method returns the current request URL, without host and protocol and relative to the front controller path.

```
DocumentRoot : /web/mysite.com/public
Front Controller Path : /web/mysite.com/public/foo/bar/index.php

Request::URL() –> http://mysite.com/foo/bar/someroute
Request::URI() –> /someroute
```

### Getting the HTTP method
---

The `Request::method()` method returns the current request HTTP method, lowercase.

```php
echo Request::method();
```

```
get
```

### Getting RAW/JSON data
---

If data was passed with the request, the method `Request::data($key=null, $default=null)` will retrieve all (if called with no parameters) data or a single property if `$key` is passed.

If requested data was empty, `$default` will be returned (resolved if callable is passed).

If request data is passed with the `Content-Type: application/json` header, will be automatically decoded.

```bash
POST /
Content-Type: application/json

{
 "name": "Chell"
}
```

```php
print_r( Response::data() );
```

```php
stdClass Object
(
    [name] => Chell
)
```