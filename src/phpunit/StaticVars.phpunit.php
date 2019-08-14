<?php


namespace eugenejonas\php_stuff\phpunit;


class UnitTest_StaticVars extends MyTestCase
{
	public function test_single_variable_reading_and_writing()
	{
		$abc =& StaticVars::abc(123);
		$this->assertEquals(123, $abc);
		
		//"@depend" should be used after this part, but it seems that
		//StaticVars state will be undefined because order of tests is not defined.
		
		$abc = 456;
		$abc2 =& StaticVars::abc(123);
		$this->assertEquals(456, $abc2);
	}
	
	public function test_two_variables_reading()
	{
		$abc =& StaticVars::abc(123);
		$this->assertEquals(123, $abc);
		
		$def =& StaticVars::def(456);
		$this->assertEquals(456, $def);
		$this->assertEquals(123, $abc);
	}
	
	public function test_that_it_saves_default_value()
	{
		$abc =& StaticVars::abc(null);
		$abc2 =& StaticVars::abc(false);
		$this->assertNull($abc);
	}
	
	public function test_unset()
	{
		$tmp =& StaticVars::abcd([]);
		$tmp[1] = 2;
		$tmp2 =& StaticVars::abcd(null);
		$this->assertEquals([1 => 2], $tmp2);
		unset($tmp2[1]);
		$tmp3 =& StaticVars::abcd(null);
		$this->assertEquals([], $tmp3);
	}
	
	public function test_appending_to_array()
	{
		$tmp =& StaticVars::efgh([]);
		$tmp []= 4;
		$tmp2 =& StaticVars::efgh([]);
		$this->assertEquals([4], $tmp2);
	}
	
	public function test_increment_operators()
	{
		$tmp =& StaticVars::fff(1);
		$tmp++;
		$tmp2 =& StaticVars::fff(0);
		$this->assertEquals(2, $tmp2);
		++$tmp2;
		$tmp3 =& StaticVars::fff(1);
		$this->assertEquals(3, $tmp3);
	}
}
