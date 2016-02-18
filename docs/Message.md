The [[Message]] module allow you pass messages between requests.

Getting a message (or all) delete them from session.

```php
Message::add('error',"This is an error.");
Message::add('info',"Testing messages!.");
Message::add('error',"Another error?.");

var_dump(
  Message::all('error'),
  Message::all(),
  Message::all() 
);
```

```
array(2) {
  [0]=> string(17) "This is an error."
  [1]=> string(14) "Another error?"
}
array(1) {
  ["info"]=> array(1) {
    [0]=> string(18) "Testing messages!."
  }
}
array(0) {
}
```

### Add a message
---

Messages must be registered to a container

```php
Message::add('error',"There was an error!");
Message::add('error',"Another one!");
```

### Get all messages
---

Get (and remove from stash) all the messages.

```php
$all_messages = Message::all();

print_r(Message::all());
```

```
false
```

### Get all messages of a kind

You can retrieve only messages of a specified container.

```php
foreach( Message::all('error') as $error ){
  echo "$error\n";
};
```

### Clear all messages

```php
Message::clear();
```

### Using messages in views
---

You can add a read-only accessor as a global view variable.

```php
View::addGlobal('Message', Message::readOnly() );
```

Now you can access messages directly in view templates via the `Message` global.

```html
{% for type,messages in Message.all() %}
  {% for text in messages %}
    <div class="errors {{ type }}">{{ text }}</div>
  {% endfor %}
{% endfor %}
```

The messages are one-shot only, only consumed they are deleted from the stash.

