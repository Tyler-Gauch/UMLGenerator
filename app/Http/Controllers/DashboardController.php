<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\GitHubHelper;
use Auth;
use App\Models\Project;
use DB;
use Log;


class DashboardController extends Controller
{
   	public function index(Request $request, $project = null)
   	{
   		$data = [];
   		if($project != null)
   		{
            $data["project"] = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();
            $data["model"] = $data["project"]->Models()->first();
            // Get the project type
            $type = $data["project"]->ProjectType;

            // echo '<pre>';
            // foreach ($type as $t) {
            //    var_dump($t);
            // }
            // echo'</pre>';

            // Get the github branches only if the project is of type github
            if ($type->name != "empty") {
                  $github = new GitHubHelper(Auth::user());
                  $data["branches"] = $github->listProjectBranches($data["project"]);
            }
   		}
   		return response()->view("dashboard", $data);
   	}

   	public function listRepos(Request $request)
   	{
   		$github = new GitHubHelper(Auth::user());

   		$repos = $github->listRepos();

   		$info = [];

   		foreach($repos as $key=>$repo){
   			$info[] = $repo->name;
   		}

         return response()->json(["success" => true, "repos" => $info]);
   	}

   	public function listBranches(Request $request)
   	{

   		$github = new GitHubHelper(Auth::user());

   		$repo = $request->input("repo", null);

   		if($repo == null)
   		{
   			return response()->json(["success" => false, "message" => "Must supply the 'repo' parameter"]);
   		}

   		$branches = $github->listBranches($repo);

   		$info = [];

   		foreach($branches as $key=>$branch){
   			$info[] = $branch->name;
   		}

		return response()->json(["success" => true, "branches" => $branches]);
   	}
}
	