<?php


namespace eugenejonas\php_stuff\app_framework\output;


class PdfOutput extends DirectlyOutputtedContent
{
	protected function getContentTypeHeader(): ?string
	{
		return 'content-type: application/pdf';
	}
	
	/**
	 * Method added for searchability.
	 */
	public function feedPdf(string $pdf)
	{
		parent::feed($pdf);
	}
}
