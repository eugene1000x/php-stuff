<?php


namespace eugenejonas\php_stuff\app_framework\output;


/**
 * Old Excel format.
 */
class XlsOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: application/vnd.ms-excel';
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedXls(string $xls)
	{
		parent::feed($xls);
	}
}
