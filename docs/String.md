The [[String]] module contains text related utility.

### Render a string template
---

Fast string templating, it uses a dot notation path for retrieving value.

Values must be enclosed in `{{ }}` double curly braces.

```php
echo String::render('Your IP is : {{ server.REMOTE_HOST }}',[
  'server' => $_SERVER
]);
```
```
Your IP is : 192.30.252.131
```