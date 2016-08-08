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
use App\Models\ModelObj;
use App\Models\ClassObj;

class ClassController extends Controller
{
   	public function create(Request $request)
   	{
   		    $className = $request->input("className", null);
          if($className == null)
          {
               return response()->json(["success" => false, "message" => "You must provide a class name"]);
          }

          $projectId = $request->input("projectId", null);
          if($projectId == null)
          {
               return response()->json(["success" => false, "message" => "You must provide a project id"]);
          }

          $project = Project::where("id", "=", $projectId)->first();

          if($project == null)
          {
               return response()->json(["success" => false, "message" => "Project not found. Invalid model id."]);
          }

          $branch = $request->input("branch", null);
          if($branch == "null")
          {
            $branch = null;
          }

          if($project->type == "github" && $branch == null)
          {
              return response()->json(["success" => false, "message" => "Invalid branch for github project"]);
          }

          $model = ModelObj::where("project_id", "=", $project->id)->where("branch", "=", $branch)->first();

          if($model == null)
          {
              return response()->json(["success" => false, "message" => "Model not found"]);   
          }

          $classObj = ClassObj::create(["name" => $className, "model_id" => $model->id, "locationX" => $request->input("x", "10"), "locationY" => $request->input("y", "10"), "type" => $request->input("classType", "class")]);

          $classObj->save();

          return response()->json(["success" => true, "id" => $classObj->id]);
      }
}
	