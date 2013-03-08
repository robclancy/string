# String

intro here guysss

## Installation
To install add the following to your `composer.json` file:

```json
"robclancy/string": "dev-master"
```

## Examples


### Notes
These examples assume you have aliased `String` to `RobClancy\String\String`. You can also replace `new String(` in these examples with `str(` if you are including `helpers.php`.

### String manipulation
```php
$string = new String('Beer!!!'); // How to create your object
// or
$string = str('Beer!!!');

// Then you can manipulate with various methods and also use chaining
$string->lower()->prepend('i love ')->upperFirst()->finish('!');

echo $string; // 'I love beer!'
```

### String checks
```php

$string = 'Love for laravel <3';

// Booleans
$string->startsWith('Love');
$string->contains('something');
$string->endsWith('<3');
$string->is('No love for laravel'); // obviously returns false!
```

### Loopage
```php

$values = new String('1,2,5,7');
foreach ($values AS $value)
{
	var_dump($value); // will return a string object with '1', then ',' etc... just loops through characters
}

foreach ($values->split(',') => $value)
{
	// this time $value will be string objects going through 1, 2, 5 etc...
}

// You can call the static String::join method to create a string object from an array of strings
$string = String::join([1, 2, 5, 7], ','); // now the same object as the original $values

// You can also manipulate by indexes
$string = new String('Teet Striing');
$string[2] = 's';
unset($string[8]);
isset($string[69]); // returns false, god I'm mature
echo $string; // 'Test String'

```


[![Build Status](https://secure.travis-ci.org/robclancy/string.png)](http://travis-ci.org/robclancy/string)
