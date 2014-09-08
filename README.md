<img src="https://github.com/caffeina-core/core/blob/master/core-logo.png?raw=true" height="200">


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/caffeina-core/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/caffeina-core/core/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/caffeina-core/core/badges/build.png?b=master)](https://scrutinizer-ci.com/g/caffeina-core/core/build-status/master)
[![Total Downloads](https://poser.pugx.org/caffeina-core/core/downloads.svg)](https://packagist.org/packages/caffeina-core/core)
[![Latest Stable Version](https://poser.pugx.org/caffeina-core/core/v/stable.svg)](https://packagist.org/packages/caffeina-core/core)
[![Latest Unstable Version](https://poser.pugx.org/caffeina-core/core/v/unstable.svg)](https://packagist.org/packages/caffeina-core/core)
[![License](https://poser.pugx.org/caffeina-core/core/license.svg)](https://packagist.org/packages/caffeina-core/core)


> Core is NOT a framework, Core is a toolkit.


## Installation


Add package to your **composer.json**:

```json
{
  "require": {
    "caffeina-core/core": "dev-master"
  }
}
```

Run [composer](https://getcomposer.org/download/):

```bash
$ php composer.phar install -o
```

Now the entire toolchain is already available upon the vendor autoloader inclusion.

```php
<?php
// Load vendors
include 'vendor/autoload.php';

Route::on('/',function(){
	echo "Hello from Core!";
});

// Dispatch route
Route::dispatch();

// Send response to the browser
Response::send();
```


## Documentation

See the [wiki](https://github.com/caffeina-core/core/wiki).


## Contributing

How to get involved:

1. [Star](https://github.com/caffeina-core/core/stargazers) the project!
2. Answer questions that come through [GitHub issues](https://github.com/caffeina-core/core/issues?state=open)
3. [Report a bug](https://github.com/caffeina-core/core/issues/new) that you find


#### Pull Requests

Pull requests are **highly appreciated**.

Solve a problem. Features are great, but even better is cleaning-up and fixing issues in the code that you discover.


<p align="center"><a href="http://caffeina.co" target="_blank" title="Caffeina - Ideas Never Sleep"><img src="https://github.com/CaffeinaLab/BrandResources/blob/master/caffeina-handmade.png?raw=true" align="center" height="65"></a></p>
