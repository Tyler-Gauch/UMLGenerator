<?php

namespace App\Helpers;

use Log;

class JavaParserHelper extends ParserHelper {

	public function __construct($fileContent)
	{
		parent::__construct($fileContent);
		$this->keywords = ["class", "public", "private", "static", "final", "package", "import", "protected", "abstract", "interface", "extends", "implements"];
	}

	public function parse(){

		$results = [
			"className" => null,
			"classType" => "class",
			"functions" => [],
			"attributes" => [],
			"relationships" => [],
			"package" => null,
			"nestedClasses" => []
		];

		$lastWord = null;
		$lastVisibility = null;
		$isAbstract = false;
		$isFinal = false;
		$isStatic = false;

		while(($word = $this->findNextKeyword()) != null)
		{
			switch($word)
			{
				case "interface":
					$results["classType"] = "interface";
				case "class":
					if($isAbstract)
					{
						$results["classType"] = "abstract";
					}
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
				case "extends":
					$results["relationships"][] = $this->sanitize($this->getNextWord());
					break;
				case "private":
				case "public":
				case "protected":
					$lastVisibility = $word;
					$isAbstract = false;
					$isFinal = false;
					$isStatic = false;
				case "abstract":
				case "final":
				case "static":

					if($word == "abstract")
					{
						$isAbstract = true;
					}
					if($word == "final")
					{
						$isFinal = true;
					}
					if($word == "static"){
						$isStatic = true;
					}

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

					if(strpos($nextWord, "(") || strpos($secondNextWord, "("))
					{
						$hasParameters = true;
						$parameters = "";
						if(strpos($nextWord, "("))
						{
							$name = str_replace(";", "", substr($nextWord, 0,  -1*(strlen($nextWord)-strpos($nextWord, "("))));		
							$parameters = substr($nextWord, strpos($nextWord, "(")+1);
							if(strpos($nextWord, ")"))
							{
								$hasParameters = false;
							}
						}else if(strpos($secondNextWord, "(")){
							$secondNextWord = $this->getNextWord();//actually move the iterator
							$parameters = substr($secondNextWord, strpos($secondNextWord, "("));
							if(strpos($secondNextWord, ")"))
							{
								$hasParameters = false;
							}
							$name = str_replace(";", "", substr($secondNextWord, 0,  -1*(strlen($secondNextWord)-strpos($secondNextWord, "("))));
						}

						$type = preg_replace('/[\s]?+(\().*/', "", $nextWord);

						if($hasParameters)
						{
							while(($word = $this->getNextWord()) != null)
							{
								$parameters .= " ".$word;
								if(strpos($word, ")"))
								{
									break;
								}
							}
						}
						$parameters = preg_replace('/[\(]?+[\)]?+/', "", $parameters);
						$parameters = explode(",", $parameters);
						Log::info(print_r($parameters, 1));
						$params = "(";
						foreach($parameters as $key=>$parameter)
						{
							$p = explode(" ", trim($parameter));
							if(isset($p[0]))
							{
								$params .= $p[0];
								if($key < count($parameters) - 1)
								{
									$params .= ", ";
								}
							}
						}
						$params = str_replace(";", "", trim($params));
						$params .= ")";
						

						$func = [
							"name" => $name,
							"visibility" => $lastVisibility,
							"isStatic" => $isStatic,
							"isFinal" => $isFinal,
							"isabstract" => $isAbstract,
							"parameters" => $params
						];
						if($name != $results["className"])
						{
							$func["type"] = $type;
							if(!in_array($type, $results["relationships"]))
							{
								$results["relationships"][] = $type;
							}
						}
						$results["functions"][] = $func;
					}else if(($attributes = $this->isAttribute($nextWord)) != null){
						foreach($attributes as $key=>$attribute)
						{
							if(!in_array($nextWord, $results["relationships"]))
							{
								$results["relationships"][] = $nextWord;
							}
							$results["attributes"][] = [
								"name" => $attribute["name"],
								"visibility" => $lastVisibility, 
								"type" => $nextWord, 
								"default" => $attribute["default"],
								"isStatic" => $isStatic,
								"isFinal" => $isFinal,
								"isabstract" => $isAbstract
							];
						}
					}
					break;
				case "package":
					$results["package"] = str_replace(";", "", $this->findNextNonKeyword());
					break;
				case "import":
					$r = $this->findNextNonKeyword();
					$results["relationships"][] = str_replace(";", "", substr($r, -1*strrchr($r, ".")));
					break;
				case "implements":
					$temp = $this->findNextNonKeyword();
					$list = $temp;
					while(strpos($temp, ","))
					{
						$temp = $this->findNextNonKeyword();
						$list .= $temp;
					}
					$list = str_replace("{", "", $list);

					$interfaces = explode(",", $list);
					foreach($interfaces as $key => $interface)
					{
						if(!in_array($interface, $results["relationships"]))
						{
							$results["relationships"][] = $interface;
						}
					}
					break;
				default:
					break;
			}
			$lastWord = $word;
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
			return [["name" => $this->sanitize($this->getNextWord()), "default" => null]];
		}

		//next thing we want to do is get everything up to the next ;
		$statement = $this->getNextStatement(true);
		//now lets remove any comments
		$statement = preg_replace('/(\/\/).*(\n)/', "", $statement);

		if(preg_match('/.*(,.*)(;)/s', $statement))
		{
			$response = [];
			//we have a comma seperated list
			$values = explode(",", $statement);
			foreach($values as $key=>$value)
			{
				$pair = explode("=", $value);
				$default = null;
				if(isset($pair[1]))
				{
					// $default = $this->sanitize($pair[1]);
					$default = $pair[1];
				}
				$response[] = ["name" => $this->sanitize($pair[0]), "default" => $default];
			}

			return $response;
		}


		if(preg_match('/[^,]+(=)?[^,]+(;)/', $statement))
		{
			$pair = explode("=", $statement);
			$default = null;
			if(isset($pair[1]))
			{
				// $default = $this->sanitize($pair[1]);
				$default = $pair[1];
			}
			return [["name" => $this->sanitize($pair[0]), "default" => $default]];
		}
		

		return null;

	}
}