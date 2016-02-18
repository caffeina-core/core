The [[Check]] module allow you to validate data in a easy way.

### Validate data
---

The `valid` method check passed keys with the defined methods, in cascade.

> The methods are a ordered priority list separated by `|`. You can pass comma separated `,` parameters to a method with the `methodname:param1,param2,"param string 3"` syntax.

```php
if (!Check::valid([
  'username' => 'required',
  'email'    => 'required | email',
  'age'      => 'required | numeric | in_range:18,90',
  'phone'    => 'numeric',
], $data_to_validate)){
   echo "Errors: " . print_r(Check::errors(),true);
} else {
   echo "OK!";
}
```


### Define a validation method
---

You can define a validation method via the `Check::method($name, callable $callback)` method.

```php
Check::method('required', function($value){
  return empty($value) ? 'This data is required.' : true;
});
```

> You can pass multiple methods in a single call via a `name => callback` associative array.

Methods are initialized on-demand, so it's preferable to define them in the `core.check.init` event.

```php
Event::on('core.check.init',function(){
  Check::method('required', function($value){
    return empty($value) ? 'This data is required.' : true;
  });
});
```

Validation methods can have parameters passed to them, you can define them after the first one which is always the full value.

```php
Check::method('in_range', function($value,$min,$max){
  return (($value>=$min)&&($value<=$max)) ? true : "This value must be in [$min,$max] range.";
});
```

### Built-in methods
---

| Method | Parameters | Description |
|--------|------------|-------------|
`required` | | The value is required _(int(0) is accepted)_
`alphanumeric` | | The value must contains only alphanumeric characters _(RegEx: \w)_
`numeric` | | The value must be a number
`email` | | The value must be a valid email
`url` | | The value must be a valid URL
`max` | `limit` | The value must be less than `limit`
`min` | `limit` | The value must be greater than `limit`
`words` | `limit` | There must be less or equal than `limit` words.
`length` | `limit` | There must be less or equal than `limit` characters.
`range` | `min` , `max` | The value is must be between or equal to [ `min` , `max` ] range
`true` | | The value must be true (check PHP manual for trueness evaluation)
`false` | | The value must be false (check PHP manual for trueness evaluation)
`same_as` | `field_name` | The value must be the same as the `field_name` value


Example:

```php
Check::valid([
  'username'    => 'required',
  'password'    => 'required',
  'password_v'  => 'required | same_as:password',
], Request::data()) || echo "Errors: " . print_r(Check::errors(),true);
```