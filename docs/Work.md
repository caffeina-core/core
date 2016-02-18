The [[Work]] module allow you to execute multiple jobs in parallel, via [cooperative multitasking](http://en.wikipedia.org/wiki/Cooperative_multitasking).

**Notice:**
> This feature needs [Generators](http://php.net/manual/it/language.generators.overview.php), so PHP version must be at least 5.5 to be used, a fatal error is triggered if version requirement is not met.

### Add a worker
---

Workers must be generators, so the `yield` keywords must be present. When a worker is registered, an object of class `TaskCoroutine` is returned.

```php
Work::add(function(){
  for ($i = 0; $i <= 10; $i++) {
    echo "Value: $i \n";
    yield; // Pass control to other workers
  }
});
```

You can also assign a name to the worker : 

```php
Work::add('the_counter',function(){
  for ($i = 0; $i <= 10; $i++) {
    echo "Value: $i \n";
    yield; // Pass control to other workers
  }
});
```


### Run and resolve all workers
---

All registered workers will be executed and the method will release control after all of the workers have finished their job.

```php
Work::run();
```

### Example
---

```php
Work::add(function(){
  for ($i = 0; $i <= 20; $i++) {
    echo "A: $i \n";
    yield;
  }
});

Work::add(function(){
  for ($i = 0; $i <= 10; $i++) {
    echo "B: $i \n";
    yield;
  }
});

// Run the workers
Work::run();
```

Output:

```
A:0
B:0
A:1
B:1
A:2
B:2
A:3
B:3
A:4
B:4
A:5
B:5
A:6
B:6
A:7
B:7
A:8
B:8
A:9
B:9
A:10
B:10
A:11
A:12
A:13
A:14
A:15
A:16
A:17
A:18
A:19
A:20
```