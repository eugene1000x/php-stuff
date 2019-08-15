<?php


namespace eugenejonas\php_stuff\app_framework\output;


/**
 * May be used both as whole html page and as html fragment in ajax requests.
 */
class HtmlOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return null;
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedHtml(string $html)
	{
		parent::feed($html);
	}
}
