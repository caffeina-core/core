The [[View]] module handles the rendering of templates via various engines.

Core ships a vanilla PHP template engine ([[PHPView]]), you can find other bridges in the [caffeina-core](https://github.com/caffeina-core) repository

### Init view engine
---

You can select a view engine with the `View::using($engine_instance)` method.

```php
View::using(new PHPView(__DIR__.'/templates'));
```

### Create a view
---

You can create a view object the `View::from($template_name)` factory method.

The `$template_name` is the relative path of the template inside the template directory.

> Extension **must be omitted**, it's automatically handled by the engine.

```php
// Prepares /templates/index.php
$index_page = View::from('index');

// Prepares /templates/errors/404.error.php
$error_page = View::from('errors/404.error');
```

### Rendering a view
---

A view renders itself when casted to a string.

```php
echo View::from('index');
```

### Passing data to a view
---

You can pass data to a view via the `with(array $variables)` method.

```php
echo View::from('index')->with([
  'title' => 'Index page',
  'toc'   => [
     'First',
     'Second',
     'Third',
   ],
]);
```

You can use the passed variables directly in the template (example uses the [twig engine](https://github.com/caffeina-core/twig) )

```html
<h1>{{ title }}</h1>
<ul>
 {% for item in toc %}
   <li>{{ item }}</li>
 {% endfor %}
</ul>
```

Renders

```html
<h1>Index page</h1>
<ul>
   <li>First</li>
   <li>Second</li>
   <li>Third</li>
</ul>
```

### Create a view with parameters shorthand
---

As a shorthand you can pass parameters to the view directly to the `from` factory method.

```php
echo View::from('index',[
  'title' => 'Index page',
]);