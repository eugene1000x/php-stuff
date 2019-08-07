<?php


namespace php_stuff;


class UnitTest_TimePeriod extends \PHPUnit\Framework\TestCase//UnitTest
{
	public function test_constructor()
	{
		$tp = new TimePeriod(mktime(0, 0, 0, 12, 4, 2004), mktime(7, 5, 15, 12, 4, 2004));
		$this->assertEquals(mktime(7, 5, 15, 12, 4, 2004), $tp->getEndTime());
	}
	
	public function test_createFromDay()
	{
		$tp = TimePeriod::createFromDay(mktime(0, 0, 0, 12, 4, 2004));
		$this->assertEquals(mktime(0, 0, 0, 12, 5, 2004), $tp->getEndTime());
	}

	public function test_createFromDayPeriod()
	{
		$tp = TimePeriod::createFromDayPeriod(mktime(0, 0, 0, 12, 4, 2004), mktime(0, 0, 0, 12, 7, 2004));
		$this->assertEquals(mktime(0, 0, 0, 12, 7, 2004), $tp->getEndTime());
	}

	public function test_createFromHour()
	{
		$tp = TimePeriod::createFromHour(mktime(10, 0, 0, 12, 4, 2004));
		$this->assertEquals(mktime(11, 0, 0, 12, 4, 2004), $tp->getEndTime());
	}
	
	//TODO: Instantiate a db object without connecting to an actual db.
	/*public function test_createPeriodSqlCondition()
	{
		unset($GLOBALS['db']);
		createDbConnections(['db']);
		global $db;
		
		$startTime = mktime(0, 0, 0, 12, 4, 2004);
		$endTime = mktime(0, 0, 0, 12, 7, 2004);
		$tp = new TimePeriod($startTime, $endTime);
		
		$this->assertEquals(
			'('.
				'(`date` >= FROM_UNIXTIME('. $db->quote($startTime) .')'.') '.
				'AND (`date` < FROM_UNIXTIME('. $db->quote($endTime) .')'.')'.
			')',
			$tp->createPeriodSqlCondition($db, 'date')
		);
	}*/

	public function test_splitByDays_with_zero_end_time()
	{
		$startTime = mktime(4, 30, 0, 2, 10, 2012);
		$endTime = mktime(0, 0, 0, 2, 13, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array(
			new TimePeriod(mktime(4, 30, 0, 2, 10, 2012), mktime(0, 0, 0, 2, 11, 2012)),
			new TimePeriod(mktime(0, 0, 0, 2, 11, 2012), mktime(0, 0, 0, 2, 12, 2012)),
			new TimePeriod(mktime(0, 0, 0, 2, 12, 2012), mktime(0, 0, 0, 2, 13, 2012)),
		);
		
		$this->assertEquals($expected, $actual);
	}

	public function test_splitByDays_with_zero_end_time_within_one_day()
	{
		$startTime = mktime(4, 30, 0, 2, 10, 2012);
		$endTime = mktime(0, 0, 0, 2, 11, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array(
			new TimePeriod(mktime(4, 30, 0, 2, 10, 2012), mktime(0, 0, 0, 2, 11, 2012)),
		);
		
		$this->assertEquals($expected, $actual);
	}
	
	public function test_splitByDays_with_non_zero_end_time()
	{
		$startTime = mktime(4, 30, 0, 2, 10, 2012);
		$endTime = mktime(10, 0, 0, 2, 12, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array(
			new TimePeriod(mktime(4, 30, 0, 2, 10, 2012), mktime(0, 0, 0, 2, 11, 2012)),
			new TimePeriod(mktime(0, 0, 0, 2, 11, 2012), mktime(0, 0, 0, 2, 12, 2012)),
			new TimePeriod(mktime(0, 0, 0, 2, 12, 2012), mktime(10, 0, 0, 2, 12, 2012)),
		);
		
		$this->assertEquals($expected, $actual);
	}

	public function test_splitByDays_with_full_day()
	{
		$startTime = mktime(0, 0, 0, 2, 10, 2012);
		$endTime = mktime(0, 0, 0, 2, 11, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array(
			new TimePeriod(mktime(0, 0, 0, 2, 10, 2012), mktime(0, 0, 0, 2, 11, 2012)),
		);
		
		$this->assertEquals($expected, $actual);
	}

	public function test_splitByDays_with_empty_period_and_zero_times()
	{
		$startTime = mktime(0, 0, 0, 2, 10, 2012);
		$endTime = mktime(0, 0, 0, 2, 10, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array();
		
		$this->assertEquals($expected, $actual);
	}

	public function test_splitByDays_with_empty_period_and_non_zero_times()
	{
		$startTime = mktime(0, 20, 0, 2, 10, 2012);
		$endTime = mktime(0, 20, 0, 2, 10, 2012);
		$tp = new TimePeriod($startTime, $endTime);
		
		$actual = $tp->splitByDays();
		$expected = array();
		
		$this->assertEquals($expected, $actual);
	}
}
