<?php


namespace eugenejonas\php_stuff;


class ClockMock implements IClock
{
	private $currentTime;
	
	
	public function __construct(int $currentTime)
	{
		$this->setTime($currentTime);
	}
	
	public function getCurrentTime()
	{
		return $this->currentTime;
	}
	
	public function setTime(int $currentTime)
	{
		if ($this->currentTime > $currentTime)
		{
			error('ClockMock::setTime called with older time than before. Debug info: '.
					dbg(['$currentTime' => $currentTime, '$this->currentTime' => $this->currentTime]));
		}
		
		$this->currentTime = $currentTime;
	}
}
