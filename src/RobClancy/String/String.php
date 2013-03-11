<?php namespace RobClancy\String;

use Inflect\Inflect;
use Patchwork\Utf8 as u;

// TODO: better exceptions

class String implements \Countable, \ArrayAccess, \IteratorAggregate {

	protected $string;

	protected $useExceptions;

	protected static $defaultUseExceptions = false;

	public function __construct($string, $exceptions = null)
	{
		if ( ! extension_loaded('mbstring'))
		{
			throw new Exception(get_class($this).' needs the mbstring extension.');
		}

		$this->useExceptions($exceptions);

		$this->string = $string;
	}

	public static function defaultUseExceptions($exceptions)
	{
		self::$defaultUseExceptions = $exceptions;
	}

	public function useExceptions($exceptions)
	{
		$this->useExceptions = is_null($exceptions) ? self::$defaultUseExceptions : $exceptions;
	}

	public function append($string)
	{
		$this->string .= $string;

		return $this;
	}

	public function prepend($string)
	{
		$this->string = $string.$this->string;

		return $this;
	}

	/**
	 * Transliterate to ASCII.
	 *
	 * @param  string  $value
	 * @return RobClancy\String\String
	 */
	public function ascii()
	{
		$this->string = u::toAscii($this->string);

		return $this;
	}

	public function length()
	{
		return u::strlen($this->string);
	}

	public function count()
	{
		return $this->length();
	}

	public function lower()
	{
		$this->string = u::strtolower($this->string);

		return $this;
	}

	public function upper()
	{
		$this->string = u::strtoupper($this->string);

		return $this;
	}

	public function lowerFirst()
	{
		$this->string = u::lcfirst($this->string);

		return $this;
	}

	public function upperFirst()
	{
		$this->string = u::ucfirst($this->string);

		return $this;
	}

	public function plural()
	{
		$this->string = Inflect::pluralize($this->string);

		return $this;
	}

	public function singular()
	{
		$this->string = Inflect::singularize($this->string);

		return $this;
	}

	public function slug($delimiter = '-')
	{
		$this->string = Inflect::urlify($this->string, $delimiter);

		return $this;
	}

	public function upperWords()
	{
		$this->string = u::ucwords($this->string);

		return $this;
	}

	public function isLower()
	{
		return ctype_lower($this->string);
	}

	public function isUpper()
	{
		return ctype_upper($this->string);
	}

	public function studly()
	{
		return $this->replace(array('_', '-'), ' ')->upperWords()->replace(' ', '');
	}

	public function camel()
	{
		return $this->studly()->lowerFirst();
	}

	public function snake($delimiter = '_')
	{
		if ($this->isLower()) return $this;

		$this->string = preg_replace('/(.)([A-Z])/', '$1'.$delimiter.'$2', $this->string);

		return $this->lower();
	}

	public function limit($limit, $end = '...')
	{
		if ($this->length() <= $limit) return $this;

		return $this->part(0, $limit)->append($end);
	}

	public function part($start, $length = null)
	{
		$this->string = u::substr($this->string, $start, $length);

		return $this;
	}

	public function position($needle, $offset = 0, $reverse = false)
	{
		$func = $reverse ? 'strrpos' : 'strpos';

		return u::$func($this->string, $needle, $offset);
	}

	public function contains($needle)
	{
		foreach ((array) $needle AS $n)
		{
			if ($this->position($n) !== false) return $this->returnOrThrow();
		}

		return $this->returnOrThrow('The string doesn\'t contain one of the provided needles');
	}

	public function startsWith($needle)
	{
		foreach ((array) $needle AS $n)
		{
			if ($this->position($n) === 0) return $this->returnOrThrow();
		}

		return $this->returnOrThrow('The string doesn\'t start with one of the provided needles');
	}

	public function endsWith($needle)
	{
		foreach ((array) $needle AS $n)
		{
			$string = new static($n);
			if ($n == $string->part($this->length() - $string->length()))
			{
				return $this->returnOrThrow();
			}
		}

		return $this->returnOrThrow('The string doesn\'t end with one of the provided needles');
	}

	public function is($string)
	{
		if (u::strcmp($this->string, $string))
		{
			return $this->returnOrThrow();
		}

		return $this->returnOrThrow('The string doesn\'t match');
	}

	// TODO: below here needs to be redone like the above

	public function matches($pattern)
	{
		return (bool) preg_match('#^'.$pattern.'#', $this->string);
	}

	public function replace($search, $replace, $count = null)
	{
		$this->string = str_replace($search, $replace, $this->string, $count);

		return $this;
	}

	public function extension()
	{
		$string = clone $this;
		if ($pos = $string->position('.', 0, true) === false)
		{
			return false;
		}

		return $string->lower()->part($pos);
	}

	public function finish($cap)
	{
		$this->string = rtrim($this->string, $cap).$cap;

		return $this;
	}

	public function split($delimiter, $limit = null)
	{
		return static::createStrings(explode($delimiter, $this->string, $limit));
	}

	public function getIterator()
	{
		return new ArrayIterator(static::createStrings(preg_split('#(?<!^)(?!$)#u'), $this->string));
	}

	public function offsetGet($offset)
	{
		$string = clone $this;

		return $string->part($offset, 1);
	}

	public function offsetSet($offset, $value)
	{
		if ($this->length() < $offset) return;

		$string = clone $this;
		$start = $string->part(0, $offset);

		$string = clone $this;
		$end = $string->part($offset+1);

		$this->string = $start.$value.$end;

		return $this;
	}

	public function offsetUnset($offset)
	{
		$this->offsetSet($offset, '');

		return $this;
	}

	public function offsetExists($offset)
	{
		return $this->length() >= $offset;
	}

	public function isNumeric()
	{
		return is_numeric($this->string);
	}

	public function isIp()
	{
		return filter_var($this->string, FILTER_VALIDATE_IP) !== false;
	}

	public function isEmail()
	{
		return filter_var($this->string, FILTER_VALIDATE_EMAIL) !== false;
	}

	public function isUrl()
	{
		return filter_var($this->string, FILTER_VALIDATE_URL) !== false;
	}

	protected function returnOrThrow($message = false)
	{
		// TODO: use a custom exception

		if ($message)
		{
			if ($this->useExceptions) throw new \ErrorException($message);

			return false;
		}

		return $this->useExceptions ? $this : true;
	}

	public static function join(array $strings, $delimiter)
	{
		return new static(implode($delimiter, $strings));
	}

	public static function createStrings(array $strings)
	{
		foreach ($strings AS &$string)
		{
			$string = new static($string);
		}

		return $strings;
	}

	/**
	 * Generate a more truly "random" alpha-numeric string.
	 *
	 * @param  int     $length
	 * @return string
	 */
	public static function random($length = 16)
	{
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$bytes = openssl_random_pseudo_bytes($length * 2);

			if ($bytes === false)
			{
				throw new \RuntimeException('Unable to generate random string.');
			}

			return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
		}

		return static::quickRandom($length);
	}

	/**
	 * Generate a "random" alpha-numeric string.
	 *
	 * Should not be considered sufficient for cryptography, etc.
	 *
	 * @param  int     $length
	 * @return string
	 */
	public static function quickRandom($length = 16)
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}

	public function __toString()
	{
		return $this->string;
	}
}