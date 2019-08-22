<?php


function isInArrayStrict($needle, array $haystack)
{
	return in_array($needle, $haystack, true);
}

function doesContainOnce($haystack, $needle)
{
	assert(is_string($haystack), '$haystack must be string: '. dbg($haystack));
	assert(is_string($needle) && strlen($needle) > 0, 'incorrect $needle parameter: '. dbg($needle));
	
	return strlen(str_replace($needle, '', $haystack)) + strlen($needle) == strlen($haystack);
}

function doesStartWith($str, $prefix)
{
	return substr($str, 0, strlen($prefix)) == $prefix;
}

function getUtf8ByteOrderMark()
{
	$bom = pack('CCC', 0xef, 0xbb, 0xbf);
	return $bom;
}

function pointToComma($str)
{
	return str_replace('.', ',', $str);
}

function commaToPoint($str)
{
	return str_replace(',', '.', $str);
}

/**
 * 9 or "9", but not "09".
 * "-" may be in the beginning, but no "+".
 */
function isIntOrIntString($what)
{
	return is_int($what) || ((string) (int) $what) === $what;
}

/**
 * "9", "9.09" or "9.090", but not "09" or "09.09".
 * "-" may be in the beginning, but no "+".
 */
function isFloatOrFloatString($what)
{
	if (is_float($what))
	{
		return true;
	}
	elseif (is_string($what) && myPregMatch('/-?\d+((\.|,)\d+)?/', $what))		//TODO: Start/end of string in regex?
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Supposed to return the last extension if there are multiple. TODO: Check it.
 */
function getFileExtension($filePath)
{
	$pathInfo = pathinfo($filePath);
	return '.'. $pathInfo['extension'];
}

function createTmpStream($data)
{
	//http://lv1.php.net/manual/en/wrappers.php.php php://memory and php://temp
	$stream = fopen('php://temp', 'r+');
	fwrite($stream, $data);
	rewind($stream);
	return $stream;
}

/**
 * Indexes the whole array by column.
 */
function indexByColumn(array $array, $column)
{
	$res = array();
	foreach ($array as $row)
	{
		$columnValue = $row[$column];

		assert(is_string($columnValue) || is_int($columnValue), '$columnValue must be string or int: '. dbg($columnValue));
		assert(!array_key_exists($columnValue, $res), 'Column values must be unique. Debug info: '. dbg(['$array' => $array, '$column' => $column]));
		
		$res[$columnValue] = $row;
	}
	
	return $res;
}

function toUtf8($str)
{
	if (is_string($str))
	{
		// if string is not utf8
		if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
		{
			// do string conversion
			$str = iconv('windows-1257', 'UTF-8', $str);
		}
	}
	
	return $str;
}

/**
 * @return string|null Full path to the created folder or null if failed.
 */
function createUniqueFolder(string $basePath): ?string
{
	$tryCount = 0;
	$isFolderCreated = false;
	
	do
	{
		$basePath .= rand(0, 9);
		
		$isFolderCreated = @mkdir($basePath, null, true);
		if ($isFolderCreated)
		{
			break;
		}
		
		++$tryCount;
		if ($tryCount > 10000)
		{
			break;
		}
	}
	while (true);
	
	if ($isFolderCreated)
	{
		return $basePath;
	}
	else
	{
		return null;
	}
}

function myExplode($delimiter, $string)
{
	assert(is_string($string) || $string === null, '$string must be null or string: '. dbg($string));

	return ($string === '' || $string === null) ? array() : explode($delimiter, $string);
}

function myJsonDecode($json)
{
	$arr = json_decode($json, true);
	assert($arr !== null, 'Decoding json failed. Debug info: '. dbg(['$arr' => $arr, '$json' => $json]));
	return $arr;
}

function myJsonEncode($value)
{
	$json = json_encode($value);
	assert($json !== false, 'Encoding json failed. Debug info: '. dbg(['$json' => $json, '$value' => $value]));
	return $json;
}

function myPregMatch($pattern, $subject, array &$matches = null, $flags = null, $offset = null)
{
	assert(is_string($pattern), '$pattern must be string: '. dbg($pattern));
	assert(is_string($subject), '$subject must be string: '. dbg($subject));

	$match = preg_match($pattern, $subject, $matches, $flags, $offset);
	assert($match === 1 || $match === 0, '$match must be 0 or 1: '. dbg($match));
	return $match === 1;
}

/**
 * Analogue of array_column.
 * Selects one column (named as $valueColumnKey) from DB-result-ish array ($array).
 * Optionally uses a column's (usually: DB table's ID column's) values as keys for resulting array.
 */
function myArrayColumn(array $array, $valueColumnKey, $indexColumnKey = false)
{
	$a__column = array();
	foreach ($array as $rowKey => $row)
	{
		if ($indexColumnKey !== false)
		{
			$key = $row[$indexColumnKey];
		}
		else
		{
			$key = $rowKey;
		}
		
		$a__column[$key] = $row[$valueColumnKey];
	}
	
	return $a__column;
}
