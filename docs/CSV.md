The [[CSV]] module allow you to read and write Comma Separated Value data.

### Open a CSV file
---

You can open a CSV file with the `CSV::open` factory. You can pass an optional separator or a format constant as a second parameter. Default value is *auto-guessing* of the separator.

```php
$csv = CSV::open('mydata.csv')->each(function($row){
  print_r($row);
});
```

### Get all data as an array
---

With a CSV object in read-mode, the `each` method will return all data if no parameters are passed to it.

```php
$all_data = CSV::open('mydata.csv')->each();
```

### Read a single row
---

With a CSV object in read-mode, the `read` method will return a single row from the CSV file.

```php
$my_data = CSV::open('mydata.csv');

$first_row  = $my_data->read();
$second_row = $my_data->read();
```

### Create a new CSV file
---

You can create a CSV file with the `CSV::create` factory. You can pass an optional separator or a format constant as a second parameter. Default value is `CSV:STANDARD`, that is a comma `,` separated.

```php
$csv = CSV::create('characters.csv');
```

### Write rows to the CSV
---

With a CSV object in write-mode, the `write` method will accept an array or an object and will write it to the CSV file.

The `schema` method defines the headers of the table to be written (if omitted the first row keys are used instead).

When a schema is defined, every written row will be reordered and filtered to be coherent with it.

```php
$csv = CSV::create('characters.csv');

$csv->schema(['name','surname','email']);

$csv->write([
  'email'   => 'punisher@nyc.com',
  'name'    => 'Frank',
  'surname' => 'Castle',
]);

$csv->write([
  'name'    => 'King',
  'surname' => 'Pin',
  'dirty'   => 1234,
  'email'   => 'the_kingpin@nyc.com',
]);

echo $csv;
```

Returns:

```
name,surname,email
Frank,Castle,punisher@nyc.com
King,Pin,the_kingpin@nyc.com
```

### Convert a CSV file to a defined format
---

```php
// Convert an unknown-CSV to an Excel compatible one.
CSV::open('agents.csv')->convert('agents.xls',CSV::EXCEL);
```

This is pratically a shorthand for:

```php
$csv = CSV::create('agents.xls',CSV::EXCEL);
CSV::open('agents.csv')->each(function($row) use ($csv) {
  $csv->write($row);
});
```
