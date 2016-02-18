
You can define a command line interface via "command routes".

Similar to the [[Route]] module, the [[CLI]] module is responsible for this feature.

### Create a simple CLI app
---

Create a new file and give execution permissions:

```bash
$ touch myapp && chmod +x myapp
```

Write this stub into `myapp` file :

```php
#!/usr/bin/env php
<?php
// Load Core and vendors
include 'vendor/autoload.php';

// Define commands routes here...


// Run the CLI dispatcher
CLI::run();
```

### Define a command route
---

CLI routes are defined by whitespace separated fragments.

```php
CLI::on('hello',function(){
  echo "Hello, friend.",PHP_EOL;
});
```

```bash
$ ./myapp hello
Hello, friend.
```

Other "static" parameters, if passed are required for the command execution.

```php
CLI::on('hello friend',function(){
  echo "Hello, friend.",PHP_EOL;
});
```

```bash
$ ./myapp hello
Error: Command [hello] is incomplete.
$ ./myapp hello friend
Hello, friend.
```

You can extract parameter from the route by prefixing the fragment name by a semicolon ":". Extracted fragments are required and will be passed to the route callback by left-to-right position.

```php
CLI::on('hello :name',function($name){
  echo "Hello, $name.",PHP_EOL;
});
```

```bash
$ ./myapp hello
Error: Command [hello] needs more parameters.
$ ./myapp hello "Gordon Freeman"
Hello, Gordon Freeman.
```

### Read options
---

Options are position free parameters, they can be passed everywhere in the command route and are optional.

You can retrieve their value with the `CLI::input($name, $default = null)` method.

```php
CLI::on('process :filename',function($filename){
  $optimize   = CLI::input('optimize',false);
  $outputfile = CLI::input('output',$filename.'.out');
  
  $data = file_get_contents($filename);
  /* process $data */
  if ($optimize) { /* optimize data */ };
  file_put_contents($outputfile,$data);
});
```

```bash
./myapp process --optimize ./test.html --output=test_opt.html
```

If you don't pass an argument for an option `--optimize`, the `true` value will be used.