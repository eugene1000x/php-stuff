<?php


namespace eugenejonas\php_stuff\phpunit;


/**
 * Can be used as replacement for functions' static variables so that they
 * can be reset between tests.
 */
class StaticVars
{
	private static $vars = array();

	
	/**
	 * Returns by reference so that caller can modify value.
	 */
	public static function &__callStatic($name, array $arguments)
	{
		$nameLowerCase = strtolower($name);
		
		if (count($arguments) !== 1)
		{
			error(
				'Only one argument must be passed - the default value, but was passed 0 or >1 arguments. Debug info: '.
						dbg(['$name' => $name, '$arguments' => $arguments])
			);
		}
		
		if (doesStartWith($nameLowerCase, '__native__'))
		{
			error('Cannot use variable names that start with __native__. This prefix is reserved. Debug info: '. dbg(['$name' => $name]));
			return;
		}
		
		
		$default = $arguments[0];
		
		if (!array_key_exists($nameLowerCase, self::$vars))
		{
			self::$vars[$nameLowerCase] = $default;
		}
		
		return self::$vars[$nameLowerCase];
	}

	public static function __native__reset()
	{
		self::$vars = array();
	}
}
