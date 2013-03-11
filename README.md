# String

A PHP library to manipulate strings via a string object similar to other languages.

This library is designed as an alternative way to using PHP's inconsistant string functions without resorting to a simple wrapper. Basically a string object like you find in other languages. Also with a `str` function to make things a little shorter/easier and hide the longer `new String` when working with strings a lot.

This library has 2 dependencies: [oodle/inflect](https://github.com/oodle/inflect) and [patchwork/utf8](https://github.com/nicolas-grekas/Patchwork-UTF8)

## Installation and Setup
To install add the following to your `composer.json` file:

```json
"robclancy/string": "dev-master"
```

Then you can use it out of the box directly with `RobClancy\String\String` or `str`. Alternatively create an alias like follows...

Native:
```php
class_alias('RobClancy\String\String', 'String');
```

Laravel, add to your aliases array in `app/config/app.php`:
```php
'String' => 'RobClancy\String\String',
```

## Examples Note: some of these aren't implemented yet, this package won't be ready for use until I write all the tests

### Class name to table name

```php
class UserGroup {
	
	public function getTable()
	{
		// We might want to point to the plural, snake case version of this class
		$class = new String(__CLASS__);

		// Snake case and split
		$words = $class->snake()->split('_');

		// Pluralize last word
		// Note: at a later stage I might have an array object which will be used here to do $words->last()->plural();
		$words[count($word)-1]->plural();

		// Now return it joined back up
		return String::join($words, '_');
	}
}
```

### Ruby styled string replace and python styled slicing

```php

$string = new String('Jason made Basset, it is pretty cool I hear, vote 1 Jason!!');

// String replace, the same as doing the key as $search and the value as $replace in $string->replace($search, $value)
$string['Jason'] = 'Jason Lewis';
$string['Basset'] = 'Basset (Better Asset Management)';

// We now want to change the 1 into 9001 but because the array notation here is overloaded to do python style slicing
// and ruby style replacing we need to force it to the replace, we do this simply by starting the replace with 'r|'
$string['r|1'] = 9001;

// Lastly let's clean it up and make it end with a single !
$string->finish('!');
// or
$string['!!'] = '!';
// or
$string->slice(0, -1);
// or the same as above with python syntax.
$string = $string[':-1'];

echo $string;
// Outputs: "Jason Lewis made Basset (Better Asset Management), it is pretty cool I hear, vote 9001 Jason Lewis!"

// Just another example of slicing with python
$string = new String('I like pizza :D');
$pizza = $string['7:-3'];
echo $pizza; // pizza

```

### Basic and quick validation with exceptions

```php

$string = 'Love for laravel <3';
$string->startsWith('Love');	// true
$string->contains('something'); // false
$string->endsWith('<3');		// true
$string->is('No love for laravel'); // obviously returns false!

// Now to show with and without exceptions
$string = new String('not_an_email');
$string->isEmail(); // This will return false

$string->useExceptions(true);
$string->isEmail(); // This will now throw an exception

// But calling that method is too verbose, so you can use a shortcut on string creation by passing true as the second argument
$string = new String('still not an email', true);

// Now any check will throw an exception so you can do quick checking and chain it like the following
try
{
	// String must be an email to do with gmail and contain the word awesome
	$string->isEmail()->endsWith('@gmail.com')->contains('awesome');
}
catch (StringException $e) // TODO: change this to whatever I call the exceptions
{
	// failed
}

// Also you can globally set the exceptions flag to be used if one is not specified, defaults to false
String::throwExceptions(true);
```

### Iteration

```php

$string = new String('It\'s Saturday, I shouldn\'t be working on this and drinking or something');

// You can loop over the string chracter by character
// Let's make the first letter of each word a capital just 'cause
$previousSpace = false;
foreach ($string AS $offset => $char)
{
	if ($char->is(' '))
	{
		$previousSpace = true;
		continue;
	}

	if ($previousSpace)
	{
		$string[$offset] = $char->upper();
	}

	$previousSpace = false;
}

echo $string; // It\'s Saturday, I Shouldn\'t Be Working On This And Drinking Or Something

// We can do your usual splits, however in this case it splits into String objects like you would expect
$words = $string->split(' '); // normal array

// Now let's do the same change as above but instead on each word, easier this time
foreach ($words AS $key => $word)
{
	$words[$key] = $word->upperFirst();
}

// Basically an alias for implode here
$string = String::join($words, ' ');
echo $string; // It\'s Saturday, I Shouldn\'t Be Working On This And Drinking Or Something
```


[![Build Status](https://secure.travis-ci.org/robclancy/string.png)](http://travis-ci.org/robclancy/string)
