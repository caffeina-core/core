
The [[Event]] module allow you to bind callbacks to custom events.

### Attach an handler
---

You can attach an handler to a named event via the `on` method.

```php
Event::on('myevent',function(){
   echo 'Hello, Friend!';
});
```

Multiple handlers can be attached to the event, they will be sequentially executed when the event will be triggered.

```php
Event::on('my.event',function(){
   echo 'First!';
});

Event::on('my.event',function(){
   echo 'Second!';
});
```
You can attach handlers to any event name.


### Trigger an event
---

You can trigger an event via the `trigger` method.

```php
Event::trigger('my.event');
```
The `trigger` method will return an array containing the return values of all the handler attached to the event.

**Example**

```php
Event::on('my.event',function(){
   return 'Hello!';
});

Event::on('my.event',function(){
   return time();
});

$results = Event::trigger('my.event');
```

The `$results` variable contains :

```php
array(2) {
 [0]  =>  string(6) "Hello!"
 [1]  =>  int(1389115191)
}
```

`NULL` will be returned if no handlers are attached to the event.

You can run a trigger only one time with the `triggerOnce` method.

### Passing parameters to event handlers
---

You can pass a variable number of parameter to event handlers appending them after the event name in the `trigger` method.

```php
Event::on('eat',function($who,$what,$where){
   echo "$who ate a $what, in the $where.";
});

Event::trigger('eat','Simon','Burrito','Kitchen');

// Result : Simon ate a Burrito, in the Kitchen

```
