<?php


namespace eugenejonas\php_stuff\app_framework\output;


class XmlOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: text/xml; charset=utf-8';
	}

	/**
	 * Method added for searchability.
	 */
	public function feedXml(string $xml)
	{
		parent::feed($xml);
	}
}
