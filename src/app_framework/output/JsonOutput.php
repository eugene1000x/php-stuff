<?php


namespace eugenejonas\php_stuff\app_framework\output;


class JsonOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: text/json; charset=utf-8';
	}
	
	public function feedJsonArr(array $jsonArr/*, $options = null, $depth = null*/)
	{
		$this->feedJsonStr(myJsonEncode($jsonArr/*, $options, $depth*/));
	}

	/**
	 * Method added for searchability.
	 */
	public function feedJsonStr(string $jsonStr)
	{
		parent::feed($jsonStr);
	}
}
