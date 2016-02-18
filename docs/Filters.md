
The [[Filters]] module allow you to permit user overrides to certain values.

### Adding a filter
---

You can attach a filter function to a custom named group via the `add` method.

```php
Filter::add('title',function($title){
   return strtoupper($title);
});
```

Multiple calls to the same group attach multiple filter functions.


### Removing a filter
---

You can remove an attached filter function to a custom named group via the `remove` method.

```php
$the_filter = function($title){
   return strtoupper($title);
};

Filter::add('title',$the_filter);

...

Filter::remove('title',$the_filter);
```

You can remove all filters attached to a group by not passing the filter function.


```php
Filter::remove('title');
```

### Applying a filter
---

You can apply a filter to a value via the `with` method.

```php
Filter::with('title','This was a triumph')
```

**Example**

```php
Filter::add('title',function($title){
   return strtoupper($title);
});

Filter::add('title',function($title){
   return $title . '!';
});

echo Filter::with('title','This was a triumph');

// THIS WAS A TRIUMPH!
```

Multiple fallback keys can be passed, the first non-empty queue will be used for the current filter.

```php
Filter::with(["document.title", "title"],'This was a triumph')
```

**Example**

```php
Filter::add("title", "strtoupper");
echo Filter::with(["document.title", "title"],'This was a triumph');
```

The `title` filter will be executed instead of the empty `document.title`.

```
THIS WAS A TRIUMPH
```

```php
Filter::add("title", "strtoupper");
Filter::add("document.title", "str_rot13");
echo Filter::with(["document.title", "title"],'This was a triumph');
```

Here the `document.title` filter will be executed instead of `title`.

```
Guvf jnf n gevhzcu
```