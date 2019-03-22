<?php


namespace php_stuff\observer;


/**
 * In method parameters, $eventContext is information about the event.
 * Its type and format are implementation-defined. $observable is the object
 * that sent the notification about the event (the object that this observer
 * subscribed to).
 */
abstract class Observer
{
	/**
	 * Default implementation, which processes all events.
	 */
	protected function isInterestingEvent(object $observable, $eventContext)
	{
		return true;
	}
	
	public abstract function onEvent(object $observable, $eventContext);
}
