<?php


namespace eugenejonas\php_stuff\app_framework\output;


class ZipOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: application/zip; charset=utf-8';
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedZip(string $zip)
	{
		parent::feed($zip);
	}
}
