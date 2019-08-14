<?php


require_once \eugenejonas\php_stuff\config\Config::$ROOT_DIR .'/src/misc.php';


use \eugenejonas\php_stuff\phpunit\MyTestCase;


class UnitTest_doesContainOnce extends MyTestCase
{
	public function getTestData()
	{
		return [
			'contains_once' => [
				'haystack' => 'abcdef',
				'needle' => 'abc',
				'expected' => true,
			],
			'contains_twice' => [
				'haystack' => 'abcdefabc',
				'needle' => 'abc',
				'expected' => false,
			],
			'does_not_contain' => [
				'haystack' => 'abcdef',
				'needle' => 'abd',
				'expected' => false,
			],
			'empty_haystack' => [
				'haystack' => '',
				'needle' => 'abc',
				'expected' => false,
			],
		];
	}
	
	/**
	 * @dataProvider getTestData
	 */
	public function test($haystack, $needle, $expected)
	{
		$actual = doesContainOnce($haystack, $needle);
		$this->assertEquals($expected, $actual);
	}
}

class UnitTest_myExplode extends MyTestCase
{
	public function getTestData()
	{
		return array(
			'empty_string' => array(
				'str' => '',
				'expected' => array(),
			),
			'null' => array(
				'str' => null,
				'expected' => array(),
			),
			'one_element' => array(
				'str' => 'abc',
				'expected' => array('abc'),
			),
			'two_elements' => array(
				'str' => 'abc,def',
				'expected' => array('abc', 'def'),
			),
		);
	}
	
	/**
	 * @dataProvider getTestData
	 */
	public function test($str, $expected)
	{
		$this->assertEquals($expected, myExplode(',', $str));
	}
}
