<?php


use \eugenejonas\php_stuff\phpunit\StaticVars;


/**
 * Rounds down time to the beginning of the day.
 * Note that it may sometimes also be at e.g. 01:00 if DST switch happens at midnight.
 */
function floorToDay($time)
{
	$cache =& StaticVars::floorToDay_cache([]);		//time, timezone => floored time

	$tz = date_default_timezone_get();
	
	assert($tz != '', 'timezone is not initialized?');
	
	$cacheKey = $time . $tz;
	
	if (!array_key_exists($cacheKey, $cache))
	{
		$datetimeInfo = getdate($time);
		$flooredTime = mktime(0, 0, 0, $datetimeInfo['mon'], $datetimeInfo['mday'], $datetimeInfo['year']);
		$cache[$cacheKey] = $flooredTime;
	}
	
	return $cache[$cacheKey];
}

function floorToHour($time)
{
	$datetimeInfo = getdate($time);
	$flooredTime = mktime($datetimeInfo['hours'], 0, 0, $datetimeInfo['mon'], $datetimeInfo['mday'], $datetimeInfo['year']);

	return $flooredTime;
}

function isOnDayBorder($time)
{
	return floorToDay($time) == $time;
}

function isOnHourBorder($time)
{
	return floorToHour($time) == $time;
}

function getNextDay($time)
{
	//Need to round after (not before) adding 1 day, because day may start at 01:00
	//because of DST switch at midnight,
	//and +1 day would give next day's 01:00, but we need 00:00.
	$res = floorToDay(strtotime('+1 day', $time));
	
	return $res;
}
