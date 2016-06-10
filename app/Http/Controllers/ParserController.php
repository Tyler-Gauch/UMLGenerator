<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ParserHelper;

class ParserController extends Controller{

	public function javaParser(Request $request){

		$fileName = $request->input("fileName", null);

		$results = [];

		if($fileName == null)
		{
			return response()->json(["success" => false, "message" => "No filename provided"]);
		}else if(!file_exists($fileName)){
			return response()->json(["success" => false, "message" => "File does not exist"]);
		}
		else{
			$results["success"] = true;
			$results["data"] = ParserHelper::getUMLInfo($fileName);
		}
		
		return response()->json($results);

	}

}