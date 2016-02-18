The [[REST]] module allow you to expose a resource via CRUD methods mapped to a RESTful API.

You must require the [API Core Bundle](/caffeina-core/api).

### Expose a resource
---

You can expose a resource via the `expose` method.

```php
REST::expose('bucket',[
  'create'  => function()   { echo "NEW bucket";          },
  'read'    => function($id){ echo "SHOW bucket($id)";    },
  'update'  => function($id){ echo "MODIFY bucket($id)";  },
  'delete'  => function($id){ echo "DELETE bucket($id)";  },
  'list'    => function()   { echo "LIST buckets";        },
  'clear'   => function()   { echo "CLEAR all buckets";   },
]);
```

Request:

```
HTTP/1.1 GET /bucket/123
```

Response:

```
SHOW bucket(123)
```

Example:

```php
REST::expose('post',[
  'create'  => function()   { return SQL::insert('posts', Input::data()); },
  'read'    => function($id){ return SQL::single('select * from posts where id=:id', ['id'=>$id]); },
  'update'  => function($id){ return SQL::update('posts', ['id'=>$id] + Input::data()); },
  'delete'  => function($id){ return SQL::delete('posts', $id); },
  'list'    => function()   { return SQL::each('select * from posts'); },
  'clear'   => function()   { return SQL::delete('posts'); },
]);
```
