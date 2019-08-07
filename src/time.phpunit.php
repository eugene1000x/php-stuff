<?php


require_once \php_stuff\config\Config::$ROOT_DIR .'/src/time.php';


class UnitTest_floorToDay extends \PHPUnit\Framework\TestCase//UnitTest
{
	public function getTestData()
	{
		return array(
			'summer_time' => array(
				'time' => strtotime('2012-05-12 15:28:05'),
				'expected' => strtotime('2012-05-12 00:00:00'),
			),
			'winter_time' => array(
				'time' => strtotime('2012-11-12 15:28:05'),
				'expected' => strtotime('2012-11-12 00:00:00'),
			),
			'dst_switch_at_midnight' => array(
				'time' => strtotime('1981-04-01 15:28:05'),
				'expected' => strtotime('1981-04-01 00:00:00'),		// In Latvia, it is actually 1981-04-01 01:00:00
			),
		);
	}
	
	/**
	 * @dataProvider getTestData
	 */
	public function test($time, $expected)
	{
		$actual = floorToDay($time);
		$this->assertEquals($expected, $actual);
	}
}

class UnitTest_floorToHour extends \PHPUnit\Framework\TestCase//UnitTest
{
	public function getTestData()
	{
		return array(
			'not_full_hour' => array(
				'timestamp' => strtotime('2013-12-17 20:15:00'),
				'expected' => strtotime('2013-12-17 20:00:00'),
			),
			'full_hour' => array(
				'timestamp' => strtotime('2013-12-17 20:00:00'),
				'expected' => strtotime('2013-12-17 20:00:00'),
			),
			'on_day_border' => array(
				'timestamp' => strtotime('2013-12-17 00:00:00'),
				'expected' => strtotime('2013-12-17 00:00:00'),
			),
		);
	}
	
	/**
	 * @dataProvider getTestData
	 */
	public function test($timestamp, $expected)
	{
		$actual = floorToHour($timestamp);
		$this->assertEquals($expected, $actual);
	}
}

class UnitTest_getNextDay extends \PHPUnit\Framework\TestCase//UnitTest
{
	public function getTestData()
	{
		return [
			'1' => array(
				'time' => 1413563713,			// 2014-10-17 19:35:13 (in Latvia)
				'expected' => 1413579600,		// 2014-10-18 00:00:00 (in Latvia)
			),
			'2' => array(
				'time' => 354916800,			// 1981-03-31 23:00:00 (in Latvia)
				'expected' => 354920400,		// 1981-04-01 01:00:00 (in Latvia) - 00:00-01:00 didn't exist because of DST switch just at midnight
			),
		];
	}
	
	/**
	 * @dataProvider getTestData
	 */
	public function test($time, $expected)
	{
		$this->assertEquals($expected, getNextDay($time));
	}
}
