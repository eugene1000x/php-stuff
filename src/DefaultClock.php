<?php


namespace eugenejonas\php_stuff;


class DefaultClock implements IClock
{
	public function getCurrentTime()
	{
		return time();
	}
}
