<?php


if (is_dir(__DIR__ .'/../vendor'))
{
	require_once __DIR__ .'/../vendor/autoload.php';
}
//else: this package is installed as dependency (it is itself in vendor dir), main project's autoload.php does everything

require_once \eugenejonas\php_stuff\config\Config::$ROOT_DIR .'/src/include.php';


enableAssertions();
//\eugenejonas\php_stuff\config\Config::init();
