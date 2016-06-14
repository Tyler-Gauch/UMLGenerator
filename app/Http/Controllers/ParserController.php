<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ParserHelper;
use App\Helpers\GitHubHelper;
use Auth;

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

	public function parseBranch(Request $request, $repo, $branch){
		$github = new GitHubHelper(Auth::user());
		$filename = $github->getArchive($repo, $branch);
		$zip = new \ZipArchive;

		if(true === $zip->open($filename.".zip")){
			unlink($filename.".zip");
			$zip->extractTo($filename);
			$results["success"] = true;
			$results["data"] = ParserHelper::getUMLInfo($filename);
			system('/bin/rm -rf ' . escapeshellarg($filename));
			return response()->json($results);
		}

		return response()->json(["success" => false, "message" => "Unkown error occurred"]);

	}

}