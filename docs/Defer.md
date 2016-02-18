The [[Defer]] module defer execution of code after the client connection has been closed.

### Run code after closing client connection
---

The passed callback will be queued for execution after the client connection has been closed.

```php
Defer::after(function(){
  some_long_operation();
});
```