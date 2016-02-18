The [[Module]] trait provides a way to extend classes, even static with new methods.

### Extend a class with new methods
---

```php
class Test {
  use Module;
  public static function Foo(){ echo "Foo"; }
}

Test::Foo(); // Foo
Test::Bar(); // Fatal error: Call to undefined method Test::Bar

Test::extend([
  'Bar' => function(){ echo "Bar"; },
]);

Test::Bar(); // Bar

```