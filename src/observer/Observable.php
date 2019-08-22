<?php


namespace eugenejonas\php_stuff\observer;


/**
 * Does not guarantee any order for notifying observers.
 */
trait Observable
{
	protected $observers = [];
	
	
	/**
	 * @param $uniquely If true, do not add the same observer more than once (will return false if trying to do so).
	 * @return true if list of observers changed.
	 */
	public function attachObserver(Observer $observer, bool $uniquely = true): bool
	{
		if (!$uniquely || array_search($observer, $this->observers, true) === false)
		{
			$this->observers []= $observer;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param $all Whether to remove all instances of the same observer or only a single instance.
	 * @return true if list of observers changed (if trying to remove a non-existent observer, will return false).
	 */
	public function detachObserver(Observer $observer, bool $all = true): bool
	{
		if ($all)
		{
			$countBefore = count($this->observers);
			$this->observers = array_filter($this->observers, function(Observer $element) use ($observer)
			{
				return $observer !== $element;
			});
			return $countBefore != count($this->observers);
		}
		else
		{
			$index = array_search($observer, $this->observers);
			
			if ($index !== false)
			{
				unset($this->observers[$index]);
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	protected function notifyObservers($eventContext)
	{
		foreach ($this->observers as $observer)
		{
			$observer->onEvent($this, $eventContext);
		}
	}
}
