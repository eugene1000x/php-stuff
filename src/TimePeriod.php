<?php


namespace php_stuff;


require_once \php_stuff\config\Config::$ROOT_DIR .'/src/intervals.php';


/**
 * Arbitrary time interval with precision to seconds.
 * Time interval is denoted as [start_datetime; end_datetime),
 * where start_datetime is inclusive, end_datetime is non-inclusive,
 * e.g., [2012.04.14 02:20:10; 2012.04.15 13:09:32).
 * 
 * Represents timestamp interval, but uses current timezone to determine day borders, etc.
 * 
 * 
 * In context of full-day interval [start_date; end_date):
 * 		* start_date is inclusive, represented as 00:00:00 of that day;
 * 		* end_date is non-inclusive, represented as 00:00:00 of the next day after the last day within the interval.
 * E.g., [2012.01.19 00:00:00; 2012.02.21 00:00:00).
 * 
 * In context of full-hour interval [start_hour; end_hour):
 * 		* start_hour is inclusive, represented as :00:00 of that hour;
 * 		* end_hour is non-inclusive, represented as :00:00 of the next hour after the last hour within the interval.
 * E.g., [2012.01.19 17:00:00; 2012.02.21 15:00:00).
 */
class TimePeriod
{
	private $startTime, $endTime;

	
	public function __construct($startTime, $endTime)
	{
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		
		assert($this->invariant());
	}
	
	private function invariant()
	{
		assert(
			$this->startTime <= $this->endTime
				//TODO: Uncomment.
				//&& isIntOrIntString($this->startTime)
				//&& isIntOrIntString($this->endTime)
				&& $this->startTime > 0		//TODO: "-123" < 0?
				&& $this->endTime > 0,
			'TimePeriod invariant does not hold. Debug info: '. dbg([
				'$this->startTime' => $this->startTime,
				'$this->endTime' => $this->endTime,
				'timezone' => date_default_timezone_get(),
			])
		);
		
		return true;
	}

	public function __toString()
	{
		return
			'['.
				date('c', $this->startTime) .'('. $this->startTime .')'.'; '.
				date('c', $this->endTime) .'('. $this->endTime .')'.
			')';
	}

	public function getStartTime()
	{
		return $this->startTime;
	}
	
	public function getEndTime()
	{
		return $this->endTime;
	}

	public function sec__getDuration()
	{
		return $this->endTime - $this->startTime;
	}

	public function getStartDate()
	{
		assert($this->isFullDayPeriod(), 'getStartDate may be called only for TimePeriod that represents full-day period; TP == '. $this);
		
		return $this->startTime;
	}
	
	public function getEndDate()
	{
		assert($this->isFullDayPeriod(), 'getEndDate may be called only for TimePeriod that represents full-day period; TP == '. $this);
	
		return $this->endTime;
	}
	
	public function isFullDayPeriod()
	{
		return isOnDayBorder($this->startTime) && isOnDayBorder($this->endTime);
	}

	public function isFullHourPeriod()
	{
		return isOnHourBorder($this->startTime) && isOnHourBorder($this->endTime);
	}
	
	/**
	 * Creates period representing single day, e.g., [2012.04.15 00:00:00; 2012.04.16 00:00:00).
	 * 
	 * @param $date Must be 00:00:00 of that day. In the above case: 2012.04.15 00:00:00.
	 */
	public static function createFromDay($date)
	{
		return self::createFromDayPeriod($date, getNextDay($date));
	}
	
	/**
	 * Creates custom full-day period, e.g., [2012.04.15 00:00:00; 2012.04.25 00:00:00).
	 * 
	 * @param $startDate Inclusive; must be 00:00:00 of the start day. In the above case: 2012.04.15 00:00:00.
	 * @param $endDate Non-inclusive; must be 00:00:00 of the next day after the last day within the interval.
	 * 		In the above case: 2012.04.25 00:00:00.
	 */
	public static function createFromDayPeriod($startDate, $endDate)
	{
		if (!isOnDayBorder($startDate))
		{
			error('$startDate is not on day border: '. dbg($startDate));
			$startDate = floorToDay($startDate);
		}
		
		if (!isOnDayBorder($endDate))
		{
			error('$endDate is not on day border: '. dbg($endDate));
			$endDate = floorToDay($endDate);		//TODO: Use ceilToDay instead of floorToDay.
		}
		
		return new TimePeriod($startDate, $endDate);
	}
	
	/**
	 * Creates period representing single hour, e.g., [2012.04.15 10:00:00; 2012.04.15 11:00:00).
	 * 
	 * @param $hour Must be :00:00 of that hour. In the above case: 2012.04.15 10:00:00.
	 */
	public static function createFromHour($hour)
	{
		return self::createFromHourPeriod($hour, $hour + 3600);
	}

	/**
	 * Creates custom full-hour period, e.g., [2012.04.15 10:00:00; 2012.04.25 07:00:00).
	 * 
	 * @param $startHour Inclusive; must be :00:00 of the start hour. In the above case: 2012.04.15 10:00:00.
	 * @param $endHour Non-inclusive; must be :00:00 of the next hour after the last hour within the interval.
	 * 		In the above case: 2012.04.25 07:00:00.
	 */
	public static function createFromHourPeriod($startHour, $endHour)
	{
		assert(isOnHourBorder($startHour), '$startHour is not a full hour: '. dbg($startHour));
		assert(isOnHourBorder($endHour), '$endHour is not a full hour: '. dbg($endHour));
		
		
		$hp = new TimePeriod($startHour, $endHour);
		
		//redundant check
		//assert($hp->isFullHourPeriod(), 'not full hour given: '. dbg(['$startHour' => $startHour, '$endHour' => $endHour]));
		
		return $hp;
	}
	
	public static function doesSpanMultipleDays($startTime, $endTime)
	{
		return $startTime != $endTime && floorToDay($startTime) != floorToDay($endTime - 1);
	}
	
	/*public function doesSpanMultipleDays()
	{
		return self::doesSpanMultipleDays($this->startTime, $this->endTime);
	}*/
	
	/**
	 * For example:
	 * 
	 * [2012.02.20 04:30:00; 2012.02.23 00:00:00)
	 * 
	 * ->
	 * 
	 * array (
	 * 		[2012.02.20 04:30:00; 2012.02.21 00:00:00),
	 * 		[2012.02.21 00:00:00; 2012.02.22 00:00:00),
	 * 		[2012.02.22 00:00:00; 2012.02.23 00:00:00),
	 * )
	 */
	public function splitByDays()
	{
		$res = array();
		$startTime = $this->startTime;
		
		while (self::doesSpanMultipleDays($startTime, $this->endTime))
		{
			$res []= new TimePeriod($startTime, getNextDay($startTime));
			$startTime = getNextDay($startTime);
		}
		
		if ($startTime != $this->endTime)
		{
			$res []= new TimePeriod($startTime, $this->endTime);
		}

		return $res;
	}
}
