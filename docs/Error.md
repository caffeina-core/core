The [[Error]] module allow you catch and manage errors.

### Starting the error handler
---

You can start the error handler via the `capture` method.

```php
Error::capture();
```

From now on errors are converted to `ErrorExceptions` and routed to the handler which dispatches them in `core.error.*` events, filtered by kind.

You can bind directly to these events via the special `Error::on*` methods.

```php
Event::on('core.error.warning',function($exception){
  syslog(LOG_WARNING,$exception->getMessage());
});
```

Preferred shortcut :

```php
Error::onWarning(function($exception){
  syslog(LOG_WARNING,$exception->getMessage());
});
```

These are the error mapping rules:

ErrorType | Gravity | Event | Method
----|------|----|----
`E_NOTICE` | Informational  | `core.error.notice` | `Error::onNotice`
`E_USER_NOTICE ` | Informational  | `core.error.notice` | `Error::onNotice`
`E_STRICT ` | Informational  | `core.error.notice` | `Error::onNotice`
`E_WARNING ` | Warning  | `core.error.warning` | `Error::onWarning`
`E_USER_WARNING ` | Warning  | `core.error.warning` | `Error::onWarning`
`E_USER_ERROR ` | Fatal  | `core.error.fatal` | `Error::onFatal`

Every error will be **also** dispatched via the `core.error` event. You can bind directly to this event via the `Error::onAny` method.

> If a single error handler returns `true`, the current error will be silenced and not propagated any more.

### Setting the display mode
---

Errors can be displayed with various formats:

Modes | Description | Example
----|------|----
`Error::SIMPLE` | Prints the error message in plain text  | `Notice: undefined variable x.`
`Error::HTML` | Prints the error message wrapped in html  | `<pre class="app error"><code>Notice: undefined variable x.</code></pre>`
`Error::SILENT` | Don't print anything  | 
`Error::JSON` | Print a JSON string of an error envelope  | `{"error":"Notice: undefined variable x."}` 


```php
Error::mode(Error::HTML);
```
