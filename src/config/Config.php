<?php


namespace eugenejonas\php_stuff\config;


class Config
{
	public static $ROOT_DIR;
}


Config::$ROOT_DIR = realpath(__DIR__ .'/../..');
