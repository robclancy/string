<?php

use Normalizer as n;
use Inflect\Inflect;
use RobClancy\String\String;

class StringTest extends PHPUnit_Framework_TestCase {

	protected static $utf8ValidityMap = array(
		"a" => true,
		"\xC3\xB1" => true,
		"\xC3\x28" => false,
		"\xA0\xA1" => false,
		"\xE2\x82\xA1" => true,
		"\xE2\x28\xA1" => false,
		"\xE2\x82\x28" => false,
		"\xF0\x90\x8C\xBC" => true,
		"\xF0\x28\x8C\xBC" => false,
		"\xF0\x90\x28\xBC" => false,
		"\xF0\x28\x8C\x28" => false,
		"\xF8\xA1\xA1\xA1\xA1" => false,
		"\xFC\xA1\xA1\xA1\xA1\xA1" => false,
	);

	public function testAppendString()
	{
		$this->assertEquals('string appended', $this->s('string')->append(' appended'));
	}

	public function testPrependString()
	{
		$this->assertEquals('prepended string', $this->s('string')->prepend('prepended '));
	}

	public function testToASCII()
	{
		$this->assertEquals('', $this->s('')->ascii());
		$this->assertEquals('deja vu', $this->s('déjà vu')->ascii());
	}

	public function testStringLength()
	{
		foreach (self::$utf8ValidityMap as $u => $t)
		{
			if ($t) $this->assertEquals(1, $this->s($u)->length());
		}

		$c = 'déjà';
		$d = n::normalize($c, n::NFD);
		$this->assertTrue($c > $d);

		$this->assertEquals(4, $this->s($c)->length());
		$this->assertEquals(4, $this->s($d)->length());

		$this->assertEquals(3, $this->s(n::normalize('한국어', n::NFD))->length());
	}

	public function testCountString()
	{
		$this->assertSame(4, count($this->s('déjà')));
	}

	public function testStringUpperAndLower()
	{
		$this->assertEquals('déjà σσς', $this->s('DÉJÀ Σσς')->lower());
		$this->assertEquals('DÉJÀ ΣΣΣ', $this->s('Déjà Σσς')->upper());
	}

	public function testFirstUpperAndLower()
	{
		$this->assertEquals('éJÀ Σσς', $this->s('ÉJÀ Σσς')->lowerFirst());
		$this->assertEquals('Éjà Σσς', $this->s('éjà Σσς')->upperFirst());
	}

	public function testSingles()
	{
		$inflections = array(
			'ox' => 'ox',
			'cats' => 'cat',
			'oxen' => 'ox',
			'cats' => 'cat',
			'purses' => 'purse',
			'analyses' => 'analysis',
			'houses' => 'house',
			'sheep' => 'sheep',
			'buses' => 'bus',
			'uses' => 'use',
			'databases' => 'database',
			'quizzes' => 'quiz',
			'matrices' => 'matrix',
			'vertices' => 'vertex',
			'alias' => 'alias',
			'aliases' => 'alias',
			'octopi' => 'octopus',
			'axes' => 'axis',
			'axis' => 'axis',
			'crises' => 'crisis',
			'crisis' => 'crisis',
			'shoes' => 'shoe',
			'foes' => 'foe',
			'pianos' => 'piano',
			'wierdos' => 'wierdo',
			'toes' => 'toe',
			'banjoes' => 'banjo',
			'vetoes' => 'veto',
		);

		foreach ($inflections as $key => $value)
		{
			$this->assertEquals($value, $this->s($key)->singular());
		}
	}

	public function testPlurals()
	{
		$inflections = array(
			'oxen' => 'ox',
			'cats' => 'cat',
			'cats' => 'cat',
			'purses' => 'purse',
			'analyses' => 'analysis',
			'houses' => 'house',
			'sheep' => 'sheep',
			'buses' => 'bus',
			'axes' => 'axis',
			'uses' => 'use',
			'databases' => 'database',
			'quizzes' => 'quiz',
			'matrices' => 'matrix',
			'vertices' => 'vertex',
			'aliases' => 'aliases',
			'aliases' => 'alias',
			'octopi' => 'octopus',
			'axes' => 'axis',
			'crises' => 'crisis',
			'crises' => 'crises',
			'shoes' => 'shoe',
			'foes' => 'foe',
			'pianos' => 'piano',
			'wierdos' => 'wierdo',
			'toes' => 'toe',
			'banjos' => 'banjo',
			'vetoes' => 'veto',
		);
		
		foreach ($inflections as $key => $value)
		{
			$this->assertEquals($key, $this->s($value)->plural());
		}
	}

	public function testSlug()
	{
		$urltests = array(
			"This Test's Apostrophe" => 'this-tests-apostrophe',
			"@#$%@##^@ @#%@#$%@#$%@#$%@#$%" => '-',
			"" => '-',
			"_+0990-0&*(&*(*)(&&*)(&*)(32@#%" => '-0990-0-and-32-',
			// FIXME: I can't get these working for some reason, to do with file encoding i think
			/*"ò" => 'o',
			"ó" => 'o',
			"ô" => 'o',
			"õ" => 'o',
			"ö" => 'o',
			"ø" => 'o',
			"ù" => 'u',
			"ú" => 'u',
			"û" => 'u',
			"ü" => 'u',
			"ý" => 'y',
			"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ" => strtolower("AAAAAAAECEEEEIIIIETHNOOOOOOUUUUYTHORNszaaaaaaaeceeeeiiiiethnoooooouuuuythorny")*/
		);

		foreach ($urltests as $key => $value)
		{
				$this->assertEquals($value, $this->s($key)->slug());
				$this->assertEquals(str_replace('-', '+', $value), $this->s($key)->slug('+'));
		}
	}

	public function testStringStudly()
	{
		$this->assertEquals('FooBar', $this->s('foo_bar')->studly());
		$this->assertEquals('FooBarBaz', $this->s('foo-bar_baz')->studly());
	}

	public function testStringCamel()
	{
		$this->assertEquals('fooBar', $this->s('foo_bar')->camel());
		$this->assertEquals('fooBarBaz', $this->s('foo-bar_baz')->camel());
	}

	public function testStringSnake()
	{
		$this->assertEquals('foo_bar', $this->s('fooBar')->snake());
		$this->assertEquals('foo-bar', $this->s('FooBar')->snake('-'));
	}

	public function testStringLimited()
	{
		$this->assertEquals('Ro...', $this->s('Robbo')->limit(2));
		$this->assertEquals('Robbo', $this->s('Robbo')->limit(5));
		$this->assertEquals('Rob___', $this->s('Robbo')->limit(3, '___'));
	}

	public function testStringPart()
	{
		$this->assertEquals('Test substring', $this->s('Test substring subness')->part(0, 14)); 
	}

	public function testStringPosition()
	{
		$this->assertEquals(0, $this->s('Robbo')->position('R'));
		$this->assertEquals(1, $this->s('Robbo')->position('o'));
		$this->assertEquals(4, $this->s('Robbo')->position('o', 2));
		$this->assertEquals(4, $this->s('Robbo')->position('o', 0, true));
		$this->assertEquals(5, $this->s('DÉJÀ Σσς')->position('Σ'));
	}

	protected function s($string)
	{
		return new String($string);
	}
}

	/*public function testStringLengthCorrect()
	{
		$this->assertEquals(5, $this->str('Robbo')->length());
		$this->assertEquals(5, $this->str('ラドクリフ')->length());
	}

	public function testStringToLower()
	{
		$this->assertEquals('robbo', 	(string)$this->str('RoBbo')->lower());
		$this->assertEquals('άχιστη', 	(string)$this->str('ΆΧΙΣΤΗ')->lower());
	}

	public function testStringToUpper()
	{
		$this->assertEquals('ROBBO', 	(string)$this->str('RoBbo')->upper());
		$this->assertEquals('ΆΧΙΣΤΗ', 	(string)$this->str('άχιστη')->upper());
	}

	public function testStringToLowerFirst()
	{
		$this->assertEquals('roBbo', 	(string)$this->str('RoBbo')->lowerFirst());
		$this->assertEquals('άΧΙΣΤΗ', 	(string)$this->str('ΆΧΙΣΤΗ')->lowerFirst());
	}

	public function testStringToUpperFirst()
	{
		$this->assertEquals('RoBbo', 	(string)$this->str('roBbo')->upperFirst());
		$this->assertEquals('ΆΧισΤη', 	(string)$this->str('άΧισΤη')->upperFirst());
	}

	protected function str($string)
	{
		return new String($string);
	}
}

/* old tests, need to keep bringing them in



	public function testStringCanBeLimitedByCharacters()
	{
		$this->assertEquals('Tay...', $this->string->limit('Taylor', 3));
		$this->assertEquals('Taylor', $this->string->limit('Taylor', 6));
		$this->assertEquals('Tay___', $this->string->limit('Taylor', 3, '___'));
	}

	public function testStringCanBeLimitedByCharactersIncludingElipses()
	{
		$this->assertEquals('T...', $this->string->limitExact('Taylor', 4));
		$this->assertEquals('Taylor', $this->string->limitExact('Taylor', 6));
		$this->assertEquals('Ta___', $this->string->limitExact('Taylor', 5, '___'));
	}

	public function testStringCanBeLimitedByWords()
	{
		$this->assertEquals('Taylor...', $this->string->limitWords('Taylor Otwell', 1));
		$this->assertEquals('Taylor___', $this->string->limitWords('Taylor Otwell', 1, '___'));
		$this->assertEquals('Taylor Otwell', $this->string->limitWords('Taylor Otwell', 3));
	}

	public function testStringCanBeWordWrapped()
	{
		$this->assertEquals('Robbo likes beer', $this->string->wordWrap('Robbo likes beer', 10));
		$this->assertEquals('Robbolikes beer', $this->string->wordWrap('Robbolikesbeer', 10));
		$this->assertEquals('Robbo likes beere speci allyw henit is hot!', $this->string->wordWrap('Robbolikesbeerespeciallywhenitis hot!', 5));
	}

	public function testStringsExtension()
	{
		$this->assertEquals('', $this->string->extension('My nEw post!!!'));
		$this->assertEquals('jpg', $this->string->extension('An img name To convert.jpg'));
		$this->assertEquals('blah', $this->string->extension('.An imgname.To-convert.blah'));
	}

	public function testStringsCanBeSlugged()
	{
		$this->assertEquals('my-new-post', $this->string->slug('My nEw post!!!'));
		$this->assertEquals('my_new_post', $this->string->slug('My nEw post!!!', '_'));
		$this->assertEquals('my-new-post', $this->string->slug('my-new-post'));
		$this->assertEquals('an-img-name-to-convertjpg', $this->string->slug('An img name To convert.jpg'));
		$this->assertEquals('an-img-name-to-convert.jpg', $this->string->slug('An img name To convert.jpg', '-', true));
	}

	public function testStringsCanBeConvertedToAscii()
	{
		$this->assertEquals('UzEJaPLae', $this->string->ascii('ŪžĒЯПĻæ'));
	}

	public function testStringsCanBeCamelCased()
	{
		$this->assertEquals('FooBar', $this->string->camelCase('foo_bar'));
		$this->assertEquals('FooBarBaz', $this->string->camelCase('foo-bar_baz'));
		$this->assertEquals('fooBar', $this->string->camelCase('foo_bar', false));
		$this->assertEquals('fooBarBaz', $this->string->camelCase('foo-bar_baz', false));
	}

	public function testStringCanBeSnakeCase()
	{
		$this->assertEquals('foo_bar', $this->string->snakeCase('fooBar'));
		$this->assertEquals('foo-bar', $this->string->snakeCase('fooBar', '-'));
	}

	public function testStringSegments()
	{
		$this->assertEquals($this->string->segments('a/path/of/words'), array(
			'a', 'path', 'of', 'words'
		));

		$this->assertEquals($this->string->segments('/a/path/of/words/'), array(
			'a', 'path', 'of', 'words'
		));
	}

	public function testRandomStringsCanBeGenerated()
	{
		$this->assertEquals(40, strlen($this->string->random(40)));
	}

	public function testStringStartsWith()
	{
		$this->assertTrue($this->string->startsWith('jason', 'jas'));
		$this->assertFalse($this->string->startsWith('jason', 'day'));
	}

	public function testStringEndsWith()
	{
		$this->assertTrue($this->string->endsWith('jason', 'on'));
		$this->assertFalse($this->string->endsWith('jason', 'no'));
	}

	public function testStringContains()
	{
		$this->assertTrue($this->string->contains('taylor', 'ylo'));
		$this->assertFalse($this->string->contains('taylor', 'xxx'));
	}

	public function testStringFinish()
	{
		$this->assertEquals('test string/', $this->string->finish('test string', '/'));
		$this->assertEquals('test stringBAM', $this->string->finish('test stringBAMBAM', 'BAM'));
		$this->assertEquals('test string/', $this->string->finish('test string/////', '/'));
	}
}*/