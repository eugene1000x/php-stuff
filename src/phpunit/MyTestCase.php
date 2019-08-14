<?php


namespace eugenejonas\php_stuff\phpunit;


abstract class MyTestCase extends \PHPUnit\Framework\TestCase
{
	protected final function setUp(): void
	{
		StaticVars::__native__reset();
		
		$this->customSetUp();
	}
	
	protected final function tearDown(): void
	{
		$this->customTearDown();
		
		//
	}
	
	protected function customSetUp(): void
	{
		//nothing by default
	}
	
	protected function customTearDown(): void
	{
		//nothing by default
	}
}
