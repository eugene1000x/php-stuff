<?php


namespace php_stuff\csv_parser;


/**
 * Processes given CSV file in order from upper line to bottom line.
 */
abstract class CsvParser
{
	protected $a__currentLine;

	private $fileHandle;
	private $delimiter;

	
	/**
	 * @param string|null $delimiter If null, then auto-detect.
	 */
	protected function __construct($fileHandle, $delimiter = null)
	{
		assert($fileHandle !== null, '$fileHandle must not be null');
		assert($delimiter === null || is_string($delimiter), '$delimiter must be string or null, but is: '. dbg($delimiter));
		
		$this->fileHandle = $fileHandle;
		$this->delimiter = $delimiter;
	}
	
	/**
	 * TODO: Make private?
	 */
	public static function determineDelimiter($csvFileHandle)
	{
		assert(is_resource($csvFileHandle), '$csvFileHandle must be resource, but is: '. dbg($csvFileHandle));
		
		$delimiters = array(',', ';', '	');
		$maxCount = -1;
		//default, in case file does not contain anything meaningful
		$res = $delimiters[0];
		
		// Tries all delimiters and looks which one is better.
		// Iterates through all lines until finds line with a delimiter.
		foreach ($delimiters as $delimiter)
		{
			$maxCountForCurrentDelimiter = -1;
			rewind($csvFileHandle);
			$line = fgetcsv($csvFileHandle, null, $delimiter);
			
			// null - if handle is invalid
			// false - on other errors, including end of file		//TODO: End of file is not error.
			while ($line !== false && $line !== null)
			{
				if (count($line) > $maxCountForCurrentDelimiter)
				{
					$maxCountForCurrentDelimiter = count($line);
				}
				
				$line = fgetcsv($csvFileHandle, null, $delimiter);
			}
			
			if ($maxCountForCurrentDelimiter > $maxCount)
			{
				$maxCount = $maxCountForCurrentDelimiter;
				$res = $delimiter;
			}
		}
		
		return $res;
	}
	
	/**
	 * Called for each CSV line once in order from upper line to bottom line.
	 * Note that CSV line is not the same thing as file line: CSV line can contain EOL.
	 */
	protected abstract function parseLine();
	
	/**
	 * Called after file has been processed (after parseLine() has been called for each line).
	 * E.g., subclass may store all lines and in the end iterate through them.
	 */
	protected function postProcess()
	{
		// do nothing by default
	}
	
	public final function handleCsvFile()
	{
		if ($this->delimiter === null)
		{
			$this->delimiter = self::determineDelimiter($this->fileHandle);
		}

		rewind($this->fileHandle);
		$this->a__currentLine = fgetcsv($this->fileHandle, null, $this->delimiter);
		
		// null - if handle is invalid
		// false - on other errors, including end of file		//TODO: End of file is not error.
		while ($this->a__currentLine !== false && $this->a__currentLine !== null)
		{
			//try
			//{
				$this->parseLine();
				$this->a__currentLine = fgetcsv($this->fileHandle, null, $this->delimiter);
			//}
			//catch ()
			//{
			//	
			//}
		}
		
		$this->postProcess();
		return $this;
	}
}
