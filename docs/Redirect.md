The [[Redirect]] module handles request agent redirection to other locations.

### HTTP Redirect
---

A simple `Location` header redirection can be achieved via the `Redirect::to($url)` method. 

```php
if ( ! Session::get('loggedUser') ) Redirect::to('/login');
```

**Warning :**
> The `to` method performs an immediate exit.

### JavaScript Redirect
---

The `Redirect::viaJavaScript($url)` method send to the browser a script for `location` redirection.

```php
Redirect::viaJavaScript('/login');
```

This outputs :

```html
<script>location.href="/login"</script>
```

> If the optional boolean parameter `$parent` is passed as `true` the `parent.location` object is used. This is useful for redirecting inside iframes, like in Facebook Page Tab apps.

