<?php


namespace eugenejonas\php_stuff;


interface ICache
{
	/**
	 * Store a mixed type value in cache for a certain amount of seconds.
	 * Allowed values are primitives and arrays.
	 * 
	 * @param mixed $value
	 */
	public function set(string $key, $value, int $sec__duration);

	/**
	 * Retrieve stored item.
	 * Returns the same type as it was stored in.
	 * Returns null if entry has expired.
	 *
	 * @return mixed|null
	 */
	public function get(string $key);
}
