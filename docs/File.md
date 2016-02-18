The [[File]] module expose utilities for working with virtual filesystems.

### Mounting Virtual Filesystems
---

You can register a Virtual Filesystem (VFS) handler via the `mount` method.

```php
File::mount($alias, $driver, $options = null)
```

The simplest VFS is [memory](#memory), a non-persistent temporary filesystem.

```php
// Mount memory VFS to 'mem' handle
File::mount('mem','memory');
```

If a VFS needs some options for setup you can pass them via a dictionary.

For example, the [native](#native) VFS, who maps an OS Filesystem path to an handle, needs the base path to be used as its root.

```php
// Mount temp directory to 'tmp' handle
File::mount('tmp','native', [
    'root' => sys_get_temp_dir(),
]);

// Mount ./app/uploads directory to 'uploads' handle
File::mount('uploads','native', [
    'root' => __DIR__.'/app/uploads',
]);
```

### Virtual Filesystems
---

#### Memory

> The Memory VFS is a volatile (non persistent) read/write temporary filesystem.

#### Native

> The Native VFS is proxy for the OS provided filesystem.

| Option | Description | Default | Mandatory |
|---|---|---|---|
| `root` | The local filesystem root absolute path | `/` | NO |


#### ZIP

> The ZIP VFS permits read/write access to the files inside a ZIP archive.

| Option | Description | Default | Mandatory |
|---|---|---|---|
| `root` | The absolute path of the ZIP archive | `TEMP-ZIP-ARCHIVE` | NO |


### File Operations
---

#### Check if a file exists : `File::exists($path)`

```php
if (! File::exists("assets://images/logo.png") ) echo "File not found.";
```

#### Read a file : `File::read($path)`

```php
$content = File::read("assets://images/logo.png");
```

#### Write a file : `File::write($path, $data)`

```php
File::write("assets://text/lorem.txt","Lorem Ipsum Dolor Sit Amet");
```

#### Append to a file : `File::append($path, $data)`

```php
File::append("assets://foobar.txt","Foo");
File::append("assets://foobar.txt","Bar");

echo File::read("assets://foobar.txt");
// FooBar

```

#### Delete a file : `File::delete($path)`

```php
File::delete("assets://useless_file.txt");
```

#### Move/Rename a file : `File::move($old_path, $new_path)`

```php
File::move("assets://files/old.txt", "assets://files/new.txt");
```

#### Search/Locate : `File::search($file_glob)`

```php
/**
 * We have 2 mounted VFS : ["mem","assets"]
 *  mem has the following tree :
 *    - /test/alpha.txt
 *    - /test/beta.png
 *    - /info.txt
 *    
 *  assets has the following tree :
 *    - /img/1.jpg
 *    - /img/2.jpg
 *    - /info.txt
 */

$texts = File::search("*.txt");
```

**Results:**

```json
[ 
  "mem://test/alpha.txt",
  "mem://info.txt"
  "assets://info.txt"
]
```
If a VFS handle is not provided an implicit search is resolved with the first result.
The priority order is the mount order of the VFS.

**Example:**

```php
File::mount('mem-1','memory');
File::mount('mem-2','memory');

File::write('mem-2://test.txt',"MEMORY 2");
File::write('mem-1://test.txt',"MEMORY 1");

echo File::read('test.txt');
```

**Results:**

```json
MEMORY 1
```
