<?php

namespace App\Helpers;

class JavaParserHelper extends ParserHelper {

	public function __construct($fileContent)
	{
		parent::__construct($fileContent);
		$this->keywords = ["class", "public", "private", "static", "final", "package", "import", "protected", "abstract"];
	}

	public function parse(){

		$results = [
			"className" => null,
			"functions" => [],
			"attributes" => [],
			"references" => [],
			"package" => null,
			"nestedClasses" => []
		];

		while(($word = $this->findNextKeyword()) != null)
		{
			switch($word)
			{
				case "class":
					if($results["className"] == null)
					{
						$results["className"] = str_replace("{", "", $this->findNextNonKeyword());
					}else{
						$this->iterator = $this->iterator-5;
						$start = $this->iterator;
						$braceCount = 1;
						for($this->iterator = strpos($this->fileContents, "{", $this->iterator)+1; $this->iterator < strlen($this->fileContents); $this->iterator++)
						{
							$this->iterator = $this->eatWhiteSpace($this->iterator);
							switch($this->fileContents[$this->iterator])
							{
								case "{":
									$braceCount++;
									break;
								case "}":
									$braceCount--;
									break;
							}

							if($braceCount == 0){
								$this->iterator++;
								break;
							}
						}
						$end = $this->iterator;
						$parser = new JavaParserHelper(substr($this->fileContents, $start, $end-$start));
						$results["nestedClasses"][] = $parser->parse();
					}
					break;
				case "private":
				case "public":
				case "protected":
				case "abstract":
					$nextWord = $this->getNextWord(true);

					if($this->isKeyWord($nextWord))
					{
						break;
					}

					//the previous call is temporary and therefore the 
					//iterator is not increased
					//hear we increment through two words again while
					//incrementing the iterator 
					//to get the proper function/attribute name
					$secondNextWord = $this->getNextWord();
					$secondNextWord = $this->getNextWord();

					if(strpos($nextWord, "("))
					{
						$results["functions"][] = str_replace(";", "", substr($nextWord, 0,  -1*(strlen($nextWord)-strpos($nextWord, "("))));
					}else if(strpos($secondNextWord, "(")){
						$results["functions"][] = str_replace(";", "", substr($secondNextWord, 0,  -1*(strlen($secondNextWord)-strpos($secondNextWord, "("))));
					}else{
						$results["attributes"][] = str_replace(";", "", $secondNextWord);
					}
					break;
				case "package":
					$results["package"] = str_replace(";", "", $this->findNextNonKeyword());
					break;
				case "import":
					$results["references"][] = str_replace(";", "", $this->findNextNonKeyword());
				default:
					break;
			}
		}

		return $results;	

	}
}