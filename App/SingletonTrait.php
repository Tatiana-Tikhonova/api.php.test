<?php

namespace App;

trait SingletonTrait
{

	protected static $unique;
	protected function __construct()
	{
	}
	public static function unique()
	{
		if (null === static::$unique) {
			static::$unique = new static;
		}
		return static::$unique;
	}
}
