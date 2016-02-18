
The [[Response]] module wrap and handles the payload sended to the request agent.

### Appending data to the response
---

Append a string to the response buffer via the `add` method.

```php
Response::add('Hello world!');
```

### Changing the content type
---

The `type` method accepts a MIME type string (or a `Response::TYPE_*` constant) for the body content type.

```php
Response::send();
```


### Adding an header to the response
---

The `header($name, $value)` method set an header for being sended to the request agent.

```php
Response::header('Authorization','Bearer mF_9.B5f-4.1JqM');
```

```
Authorization: Bearer mF_9.B5f-4.1JqM
```

### Get all defined headers
---

```php
$response_headers = Response::headers();
```

### Get response body
---

```php
$response_body = Response::body();
```

### Set the entire response body
---

You can set the entire response body by passing a parameter to the `body` method.

```php
Response::body($new_body);
```

### Set the HTTP response status
---

You can set the HTTP response status with the `status` method.

```php
Response::status(401);
```

The `error($code, $message='')` method is used to pass errors. 

> This method triggers the `core.response.error` event.

```php
Response::error(401, "User not authorized");
```