<?php


namespace eugenejonas\php_stuff\app_framework\output;


/**
 * Output that is directly echo'ed.
 * It is designed so that first the feed() method must be called
 * (zero, one or multiple times) and in the end output() must be called (once).
 */
abstract class DirectlyOutputtedContent extends Output
{
	protected $rawContent = '';
	protected $filename;
	private $feedCallCount = 0;
		
	
	/**
	 * @param string $filename If not null, output as attachment with this filename.
	 */
	public function __construct(string $filename = null)
	{
		parent::__construct();
		
		if ($filename === null)
		{
			$this->filename = null;
		}
		else
		{
			$this->filename = $this->addFilenamePrefix($filename);
		}
	}

	public function feed(string $rawContent)
	{
		$this->feedCallCount++;
		
		//TODO: Implement check if output types support appending content (trying
		//to append binary types or json may indicate logic error).
		/*if ($this-> && $this->feedCallCount > 1)
		{
			error('feed() called more than once for output type that does not support it (may indicate logic error). Debug info: '.
				dbg(['$this->feedCallCount' => $this->feedCallCount]));
		}*/
		
		$this->rawContent .= $rawContent;
	}
	
	public function output()
	{
		parent::output();
		$this->sendHeaders();
		echo $this->rawContent;
	}
	
	protected abstract function getContentTypeHeader(): ?string;

	private function getHeaders()
	{
		$headers = [
			//'pragma: no-cache',
			//'expires: 0',
			//'cache-control: private',
			//'set-cookie: fileDownload=true; path=/',			//cookie will be removed as soon as jquery.fileDownload.js onSuccess fires
		];
		//TODO: "content-encoding: utf-8" vs "content-type: ...; charset=..."
		
		if ($this->filename !== null)
		{
			$headers []= 'content-disposition: attachment; filename='. $this->filename;
		}
						
		$contentTypeHeader = $this->getContentTypeHeader();
		if ($contentTypeHeader !== null)
		{
			$headers []= $contentTypeHeader;
		}
		
		return $headers;
	}
	
	private function sendHeaders()
	{
		$headers = $this->getHeaders();
		
		foreach ($headers as $header)
		{
			header($header);
		}
	}

	private function addFilenamePrefix($filename)
	{
		/*if (isDevEnv())
		{
			$prefix = 'dev';
			$filename = $prefix .'_'. $filename;
		}*/
		
		return $filename;
	}
}
