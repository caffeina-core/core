The [[Password]] module allow you securely hash/verify password.

### Hash a password
---

```php
$hashed_passwd = Password::make('my_secret_password');
echo $hashed_passwd;
```
```
$2y$12$s88T0ByrVDPEILP2GfJUWeSqHUCFMWGFwx1XmyCguHmO2L20XuR3W
```

### Verify password
---

```php
var_dump(
  Password::verify('my_secret_password','$2y$12$s88T0ByrVDPEILP2GfJUWeSqHUCFMWGFwx1XmyCguHmO2L20XuR3W')
);
```
```
bool(true)
```