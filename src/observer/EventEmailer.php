<?php


namespace php_stuff\observer;


class EventEmailer extends Observer
{
	protected $emailOptions;
	
	
	public function __construct(array $emailOptions)
	{
		$this->emailOptions = $emailOptions;
	}
	
	public function onEvent(object $observable, $eventContext)
	{
		if ($this->isInterestingEvent($observable, $eventContext))
		{
			mail();
		}
	}
}
