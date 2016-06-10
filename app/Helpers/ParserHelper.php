<?php

namespace App\Helpers;

abstract class ParserHelper {

	protected $fileContents;
	protected $iterator = 0;
	protected $keywords;

	/**
	*@param string $fileContents
	*/
	public function __construct($fileContents){
		$this->fileContents = $fileContents;
	}

	/**
	*Parses the document for class metadata
	*
	*
	*/
	abstract public function parse();

	protected function findNextKeyword($temp = false){
		while(($nextWord = $this->getNextWord($temp)) != null){
			if($this->isKeyWord($nextWord))
			{
				return $nextWord;
			}
		}
		return null;
	}

	protected function findNextNonKeyword($temp = false){
		while(($nextWord = $this->getNextWord($temp)) != null){
			if(!$this->isKeyWord($nextWord))
			{
				return $nextWord;
			}
		}
		return null;
	}

	protected function getNextWord($temp = false){
		$word = "";
		$tempIterator = $this->eatWhiteSpace($this->iterator);
		for($tempIterator; $tempIterator < strlen($this->fileContents); $tempIterator++){

			switch($this->fileContents[$tempIterator])
			{
				case " ":
				case "\t":
				case "\n":
				case "\r":
					if(!$temp)
					{
						$this->iterator = $tempIterator;
					}
					return $word;
				default:
					$word .= $this->fileContents[$tempIterator];
					break;
			}

		}
		if(!$temp)
		{
			$this->iterator = $tempIterator;
		}
		return null;
	}

	protected function isKeyWord($word)
	{
		return in_array($word, $this->keywords);
	}

	protected function eatWhiteSpace($tempIterator){		
		for($tempIterator; $tempIterator < strlen($this->fileContents); $tempIterator++){

			switch($this->fileContents[$tempIterator])
			{
				case " ":
				case "\t":
				case "\n":
				case "\r":
					break;
				default:
					return $tempIterator;
			}

		}
		return $tempIterator;
	}

	protected static function parseDirectory($pathName, &$results){
		$dir = new \DirectoryIterator($pathName);
		foreach ($dir as $fileinfo) {
		    if (!$fileinfo->isDot()) {
		    	ParserHelper::parseFile($pathName."/".$fileinfo->getFilename(), $results);
		    }
		}
	}

	protected static function parseFile($fileName, &$results){
		if(is_dir($fileName))
		{
			ParserHelper::parseDirectory($fileName, $results);
		}else if(strpos($fileName, ".java") > -1){
    		$javaParser = new JavaParserHelper(file_get_contents($fileName));
        	$results[] = $javaParser->parse();	
		}
	}

	public static function getUMLInfo($fileName){
		$results = [];
		ParserHelper::parseFile($fileName, $results);
		return $results;
	}
}