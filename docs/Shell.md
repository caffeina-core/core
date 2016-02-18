
The [[Shell]] class can be used to execute OS commands.

### Executing a command
---

The Shell class permits the invocation of shell commands via dynamic static methods.

**Examples :**

Run the `cal` command :

```php
echo Shell::cal();
```

This outputs :

```bash
    Agosto 2014
Do Lu Ma Me Gi Ve Sa
                1  2
 3  4  5  6  7  8  9
10 11 12 13 14 15 16
17 18 19 20 21 22 23
24 25 26 27 28 29 30
31
```

#### Passing a string

You can send multiple arguments to the command by passing parameters to the method.

```php
echo Shell::ls('-la ./');

// or

echo Shell::ls('-la', './');
```

#### Passing an array

If an array is passed as an argument it will be interpreted as an options map :

```php
echo Shell::example_command([
    'silent',
    'username' => 'Gordon Freeman',
    'debug' => false,
    'profile' => true,
],'other commands');
```

Compiles to : 

```bash
/usr/bin/env example_command --silent --username='Gordon Freeman' --profile other commands
```

#### Passing a Shell object

If a Shell object is passed as an argument it will be compiled ad a direct evaluation subcommand :

```php
Shell::pipe(
    Shell::cal(),
    Shell::grep(Shell::date('+%d'))
);
```

Compiles to : 

```bash
cal | /usr/bin/env grep $(/usr/bin/env date +%d)
```

#### Lazy Execution

Shell command are not invoked immediately, but only on result evaluation (lazy invocation). You can store the prepared command in a variable for multiple use.

To invoke the command you must force the evaluation to string or call the `run` method;

```php
// Not executed.
$command = Shell::cal();

// Not executed.
$command;

// Not executed.
!$command;

// Executed.
$command->run();

// Executed.
(string)$command;

// Executed.
$x = $command . '';
```

### Get the compiled shell command
---

You can retrieve the compiled shell command via the `getShellCommand` method.

```php
echo Shell::sed('-e','"s/^ *//"','-e','"s/ *$//"')->getShellCommand();
```

Returns:

```
/usr/bin/env sed -e "s/^ *//" -e "s/ *$//"
```

### Command piping
---

If you want to concatenate multiple commands in a pipeline you must use the `pipe` method.

```php
echo Shell::pipe(
  Shell::cal(),
  Shell::grep('-E','"\d{4}"'),
  Shell::sed('-e','"s/^ *//"','-e','"s/ *$//"')
);
```

The compiled command is :

```bash
cal | /usr/bin/env grep -E "\d{4}" | /usr/bin/env sed -e "s/^ *//" -e "s/ *$//"
```

The output :

```
August 2014
```

### Command sequencing
---

Similar to command piping you can concatenate multiple shell commands via logical implication ( && ) using the `sequence` method.

```php
echo Shell::sequence(
  Shell::nginx('-t'),
  Shell::nginx('-s reload')
);
```

The compiled command is :

```bash
/usr/bin/env nginx -t && /usr/bin/env nginx -s reload
```

### Command aliases
---

You can create an alias to a complex or dynamic shell command via the `alias` method.

```php
Shell::alias('trim',function(){
    return Shell::sed('-e','"s/^ *//"','-e','"s/ *$//"');
});
```

Now you can use the alias `trim` as if it was a real command, when invoked the the callback will be called and the resulting Shell object (or raw command if you return a string) will be used in place of the alias.

**Example:**

```php
echo Shell::pipe(
  Shell::cal(),
  Shell::grep('-E','"\d{4}"'),
  Shell::trim()
);
```

Compiles as : 

```bash
cal | /usr/bin/env grep -E "\d{4}" | /usr/bin/env sed -e "s/^ *//" -e "s/ *$//"
```

Another example are some git aliases for an easy deploy system:

```php
Shell::alias('gitCommit',function($commit_message = 'Save'){
    return Shell::sequence(
        Shell::git('add -A'),
        Shell::git('commit -am',Shell::escape($commit_message))
    );
});

Shell::alias('gitCommitAndPush',function($commit_message = 'Save'){
    return Shell::sequence(
        Shell::gitCommit($commit_message),
        Shell::git('pull'),
        Shell::git('push')
    );
});
```

Now you can "save" your work with : `Shell::gitCommit()` or with a commit message `Shell::gitCommit("Latest fixes.")`;
