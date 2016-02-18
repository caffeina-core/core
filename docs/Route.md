The [[Route]] module allow you to bind callbacks to HTTP requests.

### URL mapping
---

You can define a route via the `on` method.

```php
Route::on('/hello',function(){
   echo 'Hello, Friend!';
});
```

This is the simplest form to define an `HTTP GET` route responding on URL `/hello`.
You can map the same route to multiple request methods using the fluent api interface.

```php
Route::on('/hello')
  ->via('get','post')
  ->with(function(){
     echo 'Hello, Friend!';
  });
```
The `via` method accepts an array of string for handled HTTP request methods.

The `with` method binds a callable function to the route.

If you need to map various HTTP methods to different callbacks for a single URL _(like exposing a resource via a REST API)_ the `map` method allows you to pass a `method` => `callback` dictionary.

```php
Route::map('/entity(/:id)/?',[
    'get' => function($id=null){
		// READ: fetch the $id element or all if $id === null
    },
    'post' => function($id=null){
    	// CREATE: build a new element
    },
    'put' => function($id=null){
    	// UPDATE: modify $id element's properties
    },
    'delete' => function($id=null){
    	// DELETE: delete $id element
    },
])
```

### URL pattern matching and parameters extraction
---
The route pattern is essentially a Regular Expression with some slight differencies.

The pattern is **ALWAYS** matched against the **end** of the `REQUEST_URI` parameter (stripped of the query string).

**Rules:**

- every `(...)` group becomes optional
- you can extract parameters via `:named_parameter`
- the pattern can't contain the `#` character

**Examples:**

```php
Route::on('/element(/:id)/?',function($id=null){
	if (null === $id){
		$result = get_all_elements();
	} else {
		$result = get_element_by_id($id);
	}
	print_r($result);
});
```

In this example the optional (is in a `(...)` group) `:id` is extracted when present, and the route can be optionally terminated by a `/`.

This route handles all of these request:

- `/element`
- `/element/`
- `/element/123`
- `/element/123/`
- `/element/1919191`
- `/element/462635`
- etc..

But, as you can see, this example handles also `/element/fooo`.
If we want to give format rules to an extracted parameters we can use the `rules` method.

The `rules` method accepts a `named_parameter` => `regex` dictionary.

`rules([ 'parameter_name' => 'parameter_regex_pattern' ])`

We can strenghten the former example adding rules to the `id` parameter for accepting only integer values _(defined by the \d+ regex pattern)_.

**Example:**

```php
Route::on('/element(/:id)/?',function($id=null){
	if (null === $id){
		$result = get_all_elements();
	} else {
		$result = get_element_by_id($id);
	}
	print_r($result);
})
->rules([ 'id' => '\d+' ]);
```

### Route groups
---
You can encapsulate routes based on a prefix pattern.
If the current request doesn't match the group URL pattern, relative routes definition are **not** registered.

This feature can be used for response-time optimization and for mounting route trees to a dynamic URL prefix.

You can define *nested route groups*.

**Examples:**

**Admin section**

```php
Route::group('/admin',function(){

    Route::on('/',function(){
        echo "Admin Index";
    });

    Route::on('/login')
    ->via('get','post')
    ->with(function(){
        // Handle login
    });

    Route::on('/logout',function(){
       // handle logout
    });

    Route::group('/dashboard',function(){

      Route::on('/',function(){
         // Dashboard
      });

      Route::on('/details',function(){
         // Dashboard Details
      });

    });

});
```

### Route middlewares
---
You can append a list of middlewares `before` and `after` a Route, or a RouteGroup.

> If a middleware returns `false` the entire route execution halts.

Middlewares can be chained, the `before`s will be executed in *reverse declaration order* (FIFO), the `after`s in  *direct declaration order* (LIFO).


```php
Route::on('/',
	"[TEST]"
)
->before(function(){
    echo "(B1)";
})
->before(function(){
    echo "(B2)";
})
->after(function(){
    echo "(A1)";
})
->after(function(){
    echo "(A2)";
});
```

Gives this output :

```
(B2)(B1)[TEST](A1)(A2)
```

You can apply a middleware to multiple routes a single time using a RouteGroup :

```php
Route::group('/private',function(){
    
    Route::on('/', ... );
    Route::on('/dashboard', ... );
    Route::on('/profile', ... );
    Route::on('/settings', ... );

})->before(function(){
    if ( ! user_authorized() ) {
        Response::error(403,"Forbidden");
        return false;
    }
});
```

### Dispatching routes
---

Remember to invoke the `dispatch` method before script end for route execution.

```php
Route::on('/hello',function(){
   echo 'Hello, Friend!';
});

// Run the route dispatcher.
Route::dispatch();
```


### Route can't find a match. (HTTP 404)
---

When no routes matches the current request, the `404` event is triggered.

You can append a view to the `Response` to show a courtesy page.

```php
Event::on(404,function(){
  Response::html( View::from('errors/404') );
});
```

### Render shortcuts
---

Instead of the rendering callback, you can also pass a string or a view, for direct rendering.

**Closure**

```php
Route::on('/',function(){
  return View::from('index');
});
```
```
<h1>I'm the index!</h1>
```

**A `View`**

```php
Route::on('/', View::from('index') );
```
```
<h1>I'm the index!</h1>
```

**A string _(not a callable one)_**

```php
Route::on('/', 'Not a callable string' );
```
```
Not a callable string
```

**An object**

```php
Route::on('/',(object)[
  'alpha' => 123,
  'beta'  => [1,2,3]
]);
```
```
{"alpha":123,"beta":[1,2,3]}
```