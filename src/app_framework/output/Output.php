<?php


namespace eugenejonas\php_stuff\app_framework\output;


/**
 * Represents abstract output of application (redirect or json/html or whatever).
 */
abstract class Output
{
	private $outputMethodCallCount = 0;
	private static $outputObjectsCreatedCount = 0;

	
	public function __construct()
	{
		++self::$outputObjectsCreatedCount;
	}
	
	public function output()
	{
		++$this->outputMethodCallCount;
	}

	public function __debug__getOutputMethodCallCount()
	{
		return $this->outputMethodCallCount;
	}

	public static function __debug__getObjectsCreatedCount()
	{
		return self::$outputObjectsCreatedCount;
	}
}
