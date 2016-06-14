<?php

namespace App\Helpers;

use Log;

class JavaParserHelper extends ParserHelper {

	public function __construct($fileContent)
	{
		parent::__construct($fileContent);
		$this->keywords = ["class", "public", "private", "static", "final", "package", "import", "protected", "abstract", "interface"];
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
				case "interface":
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

					$nextWord = $this->getNextWord();//actually move the iterator

					//the previous call is temporary and therefore the 
					//iterator is not increased
					//hear we increment through two words again while
					//incrementing the iterator 
					//to get the proper function/attribute name
					$secondNextWord = $this->getNextWord(true);

					if(strpos($nextWord, "("))
					{
						$results["functions"][] = str_replace(";", "", substr($nextWord, 0,  -1*(strlen($nextWord)-strpos($nextWord, "("))));
					}else if(strpos($secondNextWord, "(")){
						$secondNextWord = $this->getNextWord();//actually move the iterator
						$results["functions"][] = str_replace(";", "", substr($secondNextWord, 0,  -1*(strlen($secondNextWord)-strpos($secondNextWord, "("))));
					}else if(($attributes = $this->isAttribute($nextWord)) != null){
						Log::info(print_r($attributes,1));
						foreach($attributes as $key=>$attribute)
						{
							$results["attributes"][] = $attribute;
						}
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

	protected function eatComments($tempIterator){
		$blockComment = false;
		$regularComment = false;
		for($tempIterator; $tempIterator < strlen($this->fileContents); $tempIterator++){


			if($regularComment && $this->fileContents[$tempIterator] == "\n")
			{
				//incase we have a bunch of lines of comments
				$tempIterator = $this->eatWhiteSpace($tempIterator);
				return $this->eatComments($tempIterator);
			}else if($blockComment && $this->fileContents[$tempIterator] == "*" && $this->fileContents[$tempIterator+1] == "/")
			{
				$tempIterator = $this->eatWhiteSpace($tempIterator);
				return $this->eatComments($tempIterator);
			}else if(!$regularComment && !$blockComment){	
				switch($this->fileContents[$tempIterator])
				{
					case "/":
						$tempIterator++;
						switch($this->fileContents[$tempIterator])
						{
							case "/":
								$regularComment = true;
								break;
							case "*":
								$blockComment = true;
								break;
							default:
								return $tempIterator-2;
						}
						break;
					default:
						return $tempIterator;
				}
			}
		}
		return $tempIterator;
	}

	private function isAttribute($word)
	{
		//in order to see if we have an attribute we need to check for a few conditions
		//1) name;
		//2) name1, name2,...nameN;
		//3) name=0;
		//4) name =0;
		//5) name = 0;
		//6) name= 0;
		//7) name = new Object();
		//8) name= new Object();
		//9) name=new Object();
		//10) name =new Object();
		//11) name[] = new int[];
		//12) name[]= new int[];
		//13) name[] =new int[];
		//14) name[]=new int[];
		//15) name[]''new int[]{....};

		if(strpos($word, ";"))
		{
			return [$this->sanitize($this->getNextWord())];
		}

		//next thing we want to do is get everything up to the next ;
		$statement = $this->getNextStatement(true);
		//now lets remove any comments
		$statement = preg_replace('/(\/\/).*(\n)/', "", $statement);
		Log::info("Statement: $statement");

		if(preg_match('/.*(,.*)(;)/s', $statement))
		{
			Log::info("Matched second preg");
			$response = [];
			//we have a comma seperated list
			$values = explode(",", $statement);
			foreach($values as $key=>$value)
			{
				$pair = explode("=", $value);
				$response[] = $this->sanitize($pair[0]);
			}

			return $response;
		}


		if(preg_match('/[^,]+(=)?[^,]+(;)/', $statement))
		{
			Log::info("Matched first preg");
			return [$this->sanitize($this->getNextWord())];
		}
		

		return null;

	}
}