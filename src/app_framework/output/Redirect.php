<?php


namespace eugenejonas\php_stuff\app_framework\output;


class Redirect extends Output
{
	protected $url;
	
	
	public function __construct(string $url)
	{
		parent::__construct();
		$this->url = $url;
	}
	
	public function output()
	{
		parent::output();
		header('location: '. $this->url);
	}
}
