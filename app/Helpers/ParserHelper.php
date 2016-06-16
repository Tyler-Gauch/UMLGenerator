<?php

namespace App\Helpers;

use Log;

abstract class ParserHelper {

	protected $fileContents;
	protected $iterator = 0;
	protected $keywords;
	protected $statement_seperator = ";";

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

	protected function getNextStatement($temp = false){
		$word = "";
		$tempIterator = $this->eatWhiteSpace($this->iterator);
		$tempIterator = $this->eatComments($tempIterator);
		for($tempIterator; $tempIterator < strlen($this->fileContents); $tempIterator++){

			switch($this->fileContents[$tempIterator])
			{
				case $this->statement_seperator:
				$tempIterator++;
					if(!$temp)
					{
						$this->iterator = $tempIterator;
					}
					$word .= ";";
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

	protected function getNextWord($temp = false){
		$word = "";
		$tempIterator = $this->eatWhiteSpace($this->iterator);
		$tempIterator = $this->eatComments($tempIterator);
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
					// Log::info("Returning: $word");
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

	protected abstract function eatComments($tempIterator);

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
        	$result = $javaParser->parse();	
        	$result["fileName"] = $fileName;
        	$results[] = $result;
		}
	}

	public static function getUMLInfo($fileName){
		$results = [];
		ParserHelper::parseFile($fileName, $results);

		//now we want to see if any class references any other class
		//there must be a better way of doing this but im tired
		//and can't think of it right now

		foreach($results as $key=>$class)
		{
			foreach($results as $key2=>$class2)
			{
				Log::info("{$class['className']} referenced by {$class2['className']}?");
				if($class2["className"] == $class["className"])
				{
					continue;
				}

				$contents = file_get_contents($class2["fileName"]);
				if(strpos($contents, $class["className"]))
				{
					Log::info("YES IT DOES");
					$results[$key2]["relationships"][] = $class["className"];
				}
			}
		}

		return $results;
	}

	protected function sanitize($word){
		$word = preg_replace('/(\s|'.$this->statement_seperator.'|\[|\]|\{|\}|=|,)+/s', "", $word);
		return $word;
	}
}