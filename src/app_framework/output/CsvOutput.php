<?php


namespace eugenejonas\php_stuff\app_framework\output;


class CsvOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: text/csv; charset=utf-8';
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedCsv(string $csv)
	{
		parent::feed($csv);
	}
}
