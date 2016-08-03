<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\GitHubHelper;
use Auth;
use App\Models\Project;

class DashboardController extends Controller
{
   	public function index(Request $request, $project = null)
   	{
   		$data = [];
   		if($project != null)
   		{
   			$data["project"] = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();

            if($data["project"]->ProjectType->name == "github")
            {
   			   $github = new GitHubHelper(Auth::user());
   			   $data["branches"] = $github->listBranches($project);
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
	