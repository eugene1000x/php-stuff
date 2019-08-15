<?php


namespace eugenejonas\php_stuff\app_framework\output;


class XlsxOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';		// .xlsx
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedXlsx(string $xlsx)
	{
		parent::feed($xlsx);
	}
}
