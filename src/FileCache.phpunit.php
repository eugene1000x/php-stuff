<?php


namespace eugenejonas\php_stuff;


class UnitTest_FileCache extends \PHPUnit\Framework\TestCase
{
	private $cache;
	private $absCachePath;
	
	
	protected function setUp(): void
	{
		$this->absCachePath = $this->createTmpCacheFolder();
	}
		
	protected function tearDown(): void
	{
		if ($this->absCachePath !== null)
		{
			$this->deleteTmpCacheFolder();
		}
		
		$this->absCachePath = null;
	}
	
	private function createTmpCacheFolder()
	{
		$absCachePath = \eugenejonas\php_stuff\config\Config::$ROOT_DIR .'/tmp/cache_unit_tests_';
		$tryCount = 0;
		$isCacheFolderCreated = false;
		
		do
		{
			$absCachePath .= rand(0, 9);
			
			$isCacheFolderCreated = @mkdir($absCachePath, null, true);
			if ($isCacheFolderCreated)
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
		
		if (!$isCacheFolderCreated)
		{
			throw new \Exception('Failed to create temporary cache folder.');
		}
		else
		{
			return $absCachePath;
		}
	}
	
	private function deleteTmpCacheFolder()
	{
		$filePaths = glob($this->absCachePath .'/*');
		
		foreach ($filePaths as $filePath)
		{
			unlink($filePath);
		}
		
		rmdir($this->absCachePath);
	}
	
	public function getTestData_basic()
	{
		$maxLengthKey = '';
		for ($i = 0; $i < FileCache::MAX_KEY_LENGTH; ++$i)
		{
			$maxLengthKey .= $i % 10;
		}
		
		return [
			'string' => [
				'key' => '1',
				'value' => '1',
			],
			'int' => [
				'key' => '1',
				'value' => 1,
			],
			'boolean_true' => [
				'key' => '1',
				'value' => true,
			],
			'boolean_false' => [
				'key' => '1',
				'value' => false,
			],
			'float' => [
				'key' => '1',
				'value' => 15.2423,
			],
			'array' => [
				'key' => '1',
				'value' => ['a' => 1, 2 => 'b', '3' => true, 4 => false, '5' => 19.56, 0 => [1 => 2], 'b' => []],
			],
			'empty_key' => [
				'key' => '',
				'value' => 14.735,
			],
			'max_length_key' => [
				'key' => $maxLengthKey,
				'value' => true,
			],
		];
	}
	
	/**
	 * @dataProvider getTestData_basic
	 */
	public function test_basic_set_and_get($key, $value)
	{
		$clock = new ClockMock(100000);
		$this->cache = new FileCache($clock, $this->absCachePath);
		$this->cache->set($key, $value, 100);
		$this->assertTrue($value === $this->cache->get($key));
		$clock->setTime(100098);
		$this->assertTrue($value === $this->cache->get($key));
		
		//test expiration
		$clock->setTime(100101);
		$this->assertNull($this->cache->get($key));
	}
	
	//TODO: Different types.
	public function test_setting_with_same_key_and_value_and_shorter_duration()
	{
		$clock = new ClockMock(100000);
		$this->cache = new FileCache($clock, $this->absCachePath);
		$this->cache->set('abc', 2, 100);
		$clock->setTime(100001);
		$this->cache->set('abc', 2, 10);
		$clock->setTime(100020);
		$this->assertEquals(null, $this->cache->get('abc'));
	}
	
	public function test_setting_with_same_key_and_value_and_longer_duration()
	{
		$clock = new ClockMock(100000);
		$this->cache = new FileCache($clock, $this->absCachePath);
		$this->cache->set('abc', 2, 100);
		$clock->setTime(100001);
		$this->cache->set('abc', 2, 1000);
		$clock->setTime(100200);
		$this->assertEquals(2, $this->cache->get('abc'));
	}
	
	public function test_setting_with_same_key_different_value_and_shorter_duration()
	{
		$clock = new ClockMock(100000);
		$this->cache = new FileCache($clock, $this->absCachePath);
		$this->cache->set('abc', 2, 100);
		$clock->setTime(100001);
		$this->cache->set('abc', 3, 10);
		$this->assertEquals(3, $this->cache->get('abc'));
		$clock->setTime(100005);
		$this->assertEquals(3, $this->cache->get('abc'));
		$clock->setTime(100020);
		$this->assertEquals(null, $this->cache->get('abc'));
	}
	
	public function test_setting_with_same_key_different_value_and_longer_duration()
	{
		$clock = new ClockMock(100000);
		$this->cache = new FileCache($clock, $this->absCachePath);
		$this->cache->set('abc', 2, 100);
		$clock->setTime(100001);
		$this->cache->set('abc', 3, 1000);
		$clock->setTime(100200);
		$this->assertEquals(3, $this->cache->get('abc'));
	}
	
	public function getTestData_equal_values_different_types()
	{
		return [
			'integer_vs_string' => [
				'value1' => 2,
				'value2' => '2',
			],
			'integer_vs_float' => [
				'value1' => 51,
				'value2' => 51.0,
			],
			'float_vs_string' => [
				'value1' => 51.0,
				'value2' => '51.0',
			],
			'bool_vs_int_1' => [
				'value1' => 1,
				'value2' => true,
			],
			'bool_vs_int_2' => [
				'value1' => 0,
				'value2' => false,
			],
			'bool_vs_string_1' => [
				'value1' => 'true',
				'value2' => true,
			],
			'bool_vs_string_2' => [
				'value1' => 'false',
				'value2' => false,
			],
		];
	}
	
	/**
	 * @dataProvider getTestData_equal_values_different_types
	 */
	public function test_setting_with_equal_values_of_different_types__with_same_duration($value1, $value2)
	{
		$this->cache = new FileCache(null, $this->absCachePath);
		
		$this->cache->set('abc', $value1, 100);
		$this->cache->set('abc', $value2, 100);
		$this->assertTrue($value2 === $this->cache->get('abc'));
		
		$this->cache->set('def', $value2, 100);
		$this->cache->set('def', $value1, 100);
		$this->assertTrue($value1 === $this->cache->get('def'));
	}
	
	/**
	 * @dataProvider getTestData_equal_values_different_types
	 */
	public function test_setting_with_equal_values_of_different_types__with_longer_duration($value1, $value2)
	{
		$this->cache = new FileCache(null, $this->absCachePath);
		
		$this->cache->set('abc', $value1, 100);
		$this->cache->set('abc', $value2, 1000);
		$this->assertTrue($value2 === $this->cache->get('abc'));
		
		$this->cache->set('def', $value2, 100);
		$this->cache->set('def', $value1, 1000);
		$this->assertTrue($value1 === $this->cache->get('def'));
	}
	
	/**
	 * @dataProvider getTestData_equal_values_different_types
	 */
	public function test_setting_with_equal_values_of_different_types__with_shorter_duration($value1, $value2)
	{
		$this->cache = new FileCache(null, $this->absCachePath);
		
		$this->cache->set('abc', $value1, 100);
		$this->cache->set('abc', $value2, 10);
		$this->assertTrue($value2 === $this->cache->get('abc'));
		
		$this->cache->set('def', $value2, 100);
		$this->cache->set('def', $value1, 10);
		$this->assertTrue($value1 === $this->cache->get('def'));
	}
	
	public function getTestData__tricky_string_comparison()
	{
		return [
			[
				'key1' => '01234',
				'key2' => '1234',
			],
			[
				'key1' => '10',
				'key2' => '1e1',
			],
			[
				'key1' => '100',
				'key2' => '1e2',
			],
			[
				'key1' => '40',
				'key2' => '40th',
			],
			[
				'key1' => 'abc',
				'key2' => '0',
			],
			[
				'key1' => 'false',
				'key2' => '0',
			],
			[
				'key1' => 'true',
				'key2' => '1',
			],
			[
				'key1' => '1',
				'key2' => '1.0',
			],
			[
				'key1' => '1.',
				'key2' => '1',
			],
			[
				'key1' => '1.',
				'key2' => '1.0',
			],
			[
				'key1' => '1.0',
				'key2' => '1.00',
			],
			[
				'key1' => '01.23',
				'key2' => '1.23',
			],
			[
				'key1' => '1.2e3',
				'key2' => '1200',
			],
			[
				'key1' => '7E-5',
				'key2' => '0.00007',
			],
			//TODO: 0x
		];
	}
	
	/**
	 * @dataProvider getTestData__tricky_string_comparison
	 */
	public function test_with_keys_that_have_tricky_string_comparison($key1, $key2)
	{
		$this->cache = new FileCache(null, $this->absCachePath);
		
		$this->cache->set($key1, 'abc', 100);
		$this->cache->set($key2, 'def', 100);
		$this->assertEquals('abc', $this->cache->get($key1));
		$this->assertEquals('def', $this->cache->get($key2));
		
		$this->cache->set($key2, 'abc', 100);
		$this->cache->set($key1, 'def', 100);
		$this->assertEquals('abc', $this->cache->get($key2));
		$this->assertEquals('def', $this->cache->get($key1));
	}
}
