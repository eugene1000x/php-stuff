<?php


namespace php_stuff\observer;


class EventFileLogger extends Observer
{
	protected $logPath;
	
	
	public function __construct(string $logPath)
	{
		$this->logPath = $logPath;
	}
	
	public function onEvent(object $observable, $eventContext)
	{
		if ($this->isInterestingEvent($observable, $eventContext))
		{
			file_put_contents();
		}
	}
}
