<?php


/*
 * Functions that work with intervals.
 * These can be any type of intervals (time, number intervals).
 * An interval is denoted as [start; end), where start is inclusive and end is non-inclusive.
 * Precondition: start <= end.
 */


function assertIntervalCorrect($start, $end)
{
	assert($start <= $end, 'invalid interval given: '. dbg(['$start' => $start, '$end' => $end]));
}

/**
 * Whether two intervals [a1; a2) and [b1; b2) intersect.
 */
function createIntervalIntersectSqlCondition($a1, $a2, $b1, $b2)
{
	//cannot do assertion because start and end may be sql field names, etc.
	
	return '(('. $a1 .' < '. $b2 .') AND ('. $a2 .' > '. $b1 .'))';
}
function doIntervalsIntersect($a1, $a2, $b1, $b2)
{
	assertIntervalCorrect($a1, $a2);
	assertIntervalCorrect($b1, $b2);
	
	return $a1 < $b2 && $a2 > $b1;
}

/**
 * Checks if interval [a1; a2) is inside interval [b1; b2).
 */
function isIntervalInsideAnother($a1, $a2, $b1, $b2)
{
	assertIntervalCorrect($a1, $a2);
	assertIntervalCorrect($b1, $b2);
	
	return $a1 >= $b1 && $a2 <= $b2;
}
