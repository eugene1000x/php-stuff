<?php


namespace eugenejonas\php_stuff;


/**
 * File-based caching engine. Supports integer, string, boolean, float, array types.
 * Supports keys of max length FileCache::MAX_KEY_LENGTH.
 * Note: Does not clean up expired entries, but that could be done by deleting old tmp files by a cron job.
 */
class FileCache implements ICache
{
	/**
	 * Due to filename length restriction generally being 255 symbols and base64-encoding.
	 */
	public const MAX_KEY_LENGTH = 180;
	
	
	private $clock;
	private $absCachePath;		// where the files are stored
	
	
	/**
	 * @param $clock A mock can be provided for unit testing purposes.
	 * 		If null, default implementation will be used.
	 * @param $absCachePath Absolute path to the cache folder.
	 * 		If null, default path is used.
	 */
	public function __construct(IClock $clock = null, string $absCachePath = null)
	{
		if ($clock === null)
		{
			$clock = new DefaultClock();
		}
		
		if ($absCachePath === null)
		{
			$absCachePath = \eugenejonas\php_stuff\config\Config::$ROOT_DIR .'/tmp/cache';
		}
		
		$this->clock = $clock;
		$this->absCachePath = $absCachePath;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function set(string $key, $value, int $sec__duration)
	{
		if (strlen($key) > self::MAX_KEY_LENGTH)
		{
			error_log('Key too long. Debug info: '. dbg(['$key' => $key, 'self::MAX_KEY_LENGTH' => self::MAX_KEY_LENGTH]));
			return;
		}
		
		assert($sec__duration > 0, '$sec__duration must be > 0. Debug info: '. dbg(['$sec__duration' => $sec__duration]));
		
		
		$cachedTime = $this->clock->getCurrentTime();
		$entry = [
			'cached_time' => $cachedTime,
			'sec__duration' => $sec__duration,
			'value' => $value,
		];
		$filePath = $this->getFilePath($key);
		$serialized = serialize($entry);
		
		//no locking necessary here, cause this is atomic
		$res = file_put_contents($filePath, $serialized);
		if ($res === false)
		{
			error_log('Could not write to cache. Debug info: '. dbg(['$filePath' => $filePath]));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $key)
	{
		$filePath = $this->getFilePath($key);
		
		$entry = $this->readCacheEntry($filePath);
		if ($entry === null)
		{
			return null;
		}
		
		$time = $this->clock->getCurrentTime();
		
		if ($entry['cached_time'] + $entry['sec__duration'] < $time)
		{
			return null;
		}
		else
		{
			return $entry['value'];
		}
	}
	
	private function getFilePath(string $key)
	{
		//prefix with "_" to correctly handle empty keys
		$filename = '_'. str_replace('/', '-', base64_encode($key));
		$filePath = $this->absCachePath .'/'. $filename;
		
		return $filePath;
	}
	
	/**
	 * Returns null if entry does not exist or has incorrect format.
	 */
	private function readCacheEntry($filePath)
	{
		//suppressing warnings is ok here - file may not exist
		$raw = @file_get_contents($filePath);
		
		if ($raw === false)
		{
			return null;
		}
		
		//if string is not unserializable, this will generate notice, which may indicate a bug in code
		$unserialized = unserialize($raw);
		
		if (
			!is_array($unserialized)
			|| !isset($unserialized['sec__duration']) || !is_int($unserialized['sec__duration']) || $unserialized['sec__duration'] <= 0
			|| !isset($unserialized['cached_time']) || !is_int($unserialized['cached_time']) || $unserialized['cached_time'] <= 0
			|| !isset($unserialized['value'])
		)
		{
			error_log('Cache file has incorrect format. Debug info: '.
					dbg(['$unserialized' => $unserialized, '$filePath' => $filePath]));
			
			return null;
		}
		
		return $unserialized;
	}
}
