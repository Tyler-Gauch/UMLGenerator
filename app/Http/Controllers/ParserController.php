<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ParserHelper;
use App\Helpers\GitHubHelper;
use Auth;
use App\Models\Project;

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

	public function parseBranch(Request $request, $project){

		$branch = $request->input("branch", null);
		if($branch == "null")
		{
			$branch = null;
		}

		$project = Project::where("name", "=", $project)->firstOrFail();

		if($project->repo != null)
		{
			$github = new GitHubHelper(Auth::user());
			$filename = $github->getArchive($project->repo, $branch);

			if($filename == null)
			{
				return response()->json(["success" => false, "message" => "Error downloading repo.  Please try again."]);
			}
		}else if($project->url != null)
		{
			$result = GitHubHelper::downloadPublicRepo($project->url, $branch);

			if(!$result["success"])
			{
				return response()->json(["success" => false, "message" => $result["message"]]);
			}
			$filename = $result["filename"];
		}else{
			return response()->json(["success" => false, "message" => "Data error! Please Contact Support"]);
		}

		$zip = new \ZipArchive;

		if(true === $zip->open($filename.".zip")){
			unlink($filename.".zip");
			$zip->extractTo($filename);
			$results["success"] = true;
			$results["data"] = ParserHelper::getUMLInfo($filename);
			system('/bin/rm -rf ' . escapeshellarg($filename));

			//delete the current classes
			$model = $project->Models()->where("branch", "=", $branch)->first();
			$classes = $model->Classes()->get();

			foreach($classes as $key=>$class)
			{
				$class->Attributes()->delete();
				$class->Operations()->delete();
				$class->StartingRelationship()->delete();
				$class->EndingRelationship()->delete();
				$class->delete();
			}

			return response()->json($results);
		}

		return response()->json(["success" => false, "message" => "Unkown error occurred"]);
	}
}