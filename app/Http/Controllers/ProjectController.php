<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\GitHubHelper;
use Auth;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Language;
use App\Models\ClassObj;
use App\Models\Attribute;
use App\Models\Operation;
use DB;
use Log;

class ProjectController extends Controller
{
   	public function create(Request $request)
   	{
         $name = $request->input("name", null);
         $language = $request->input("language", "None");
         $type = $request->input("type", null);

         if($name == null || $name == "null" || $name == "")
         {
            return response()->json(["success" => false, "message" => "You must choose a Repository"]);
         }

         if($type != "empty" && ($language == null || $language == "null" || $language == ""))
         {
            return response()->json(["success" => false, "message" => "You must choose a Language"]);
         }

         $project = Project::where("name", $name)->where("user_id", Auth::user()->id)->firstOrCreate([
            "name" => $name,
            "language_id" => Language::where("name", "=", $language)->first()->id,
            "user_id" => Auth::user()->id,
            "project_type_id" => ProjectType::where("name", "=", $type)->first()->id
         ]);

   		return response()->json(["success" => true]);
   	}

      public function get(Request $request, $project = null){
         $data = ["success" => true];
         $projects = null;
         if($project != null)
         {
            $data["projects"] = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->get();
         }else{
            $data["projects"] = Project::where("user_id", "=", Auth::user()->id)->get();
         }

         return response()->json($data);
      }

      public function save(Request $request, $project) {
         // Get the project id of the current project and add the model
         $proj = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();
         $branch = $request->input("branch", null);

         // Get the github branch or NULL if empty project
         $model = DB::table('models')->where("project_id", "=", $proj["id"])->where("branch", "=", $branch)->get();

         // If there wasnt a model already defined, insert it
         if ($model == null) {
            $modelID = DB::table('models')->insert(
               ["branch" => $branch, "project_id" => $proj["id"]]
            );
            $model = DB::table('models')->where("id", "=", $modelID)->get()[0];
         } else {
            $model = $model[0]; // Dont want an array, so grab the first entry
         } 

         // Loop through classes from user input
         $data = $request->all();
         foreach ($data as $curObj) {
            if ($curObj["type"] == "class") {
               $className = $curObj["className"];
               $locX = $curObj["x"];  // starting x coordinate of the class 
               $locY = $curObj["y"];  // starting y coordinate of the class

               if (isset($curObj["classType"])) {
                  $classType = $curObj["classType"];
               } else {
                  $classType = "public";
               }

               $debug = "Class " . $className . " (" . $locX . ", " . $locY . "), attributes {";

               // Create the class in the database
               $curClass = ClassObj::where("name", "=", $className)->where("model_id", "=", $model->id)->firstOrNew([
                  "name" => $className,
                  "model_id" => $model->id
                  // "locationX" => $locX,
                  // "locationY" => $locY,
                  // "type" => $classType
               ]);

               // Update the values
               $curClass->locationX = $locX;
               $curClass->locationY = $locY;
               $curClass->type = $classType;

               // TODO add package

               $curClass->save();


               // Get attribute and function names
               if (isset($curObj["attributes"])) {
                  foreach ($curObj["attributes"] as $attr) {
                     $debug .= $attr . ", ";

                     // TODO - Get values from array
                     $attribute = Attribute::where("name", "=", $attr)->where("class_id", "=", $curClass->id)->firstOrNew([
                        "name" => $attr,
                        "class_id" => $curClass->id
                     ]);
                     $attribute->visibility = "public";
                     $attribute->type="int";

                     // TODO default value

                     $attribute->save();

                  }
               }

               $debug .= "}, functions {";

               if (isset($curObj["functions"])) {
                  foreach ($curObj["functions"] as $func) {
                     $debug .= $func . ", ";

                     // TODO - Get values from array
                     $operation = Operation::where("name", "=", $func)->where("class_id", "=", $curClass->id)->firstOrNew([
                        "name" => $func,
                        "class_id" => $curClass->id
                     ]);
                     $operation->visibility = "public";
                     $operation->return_type = "void";

                     // TODO parameters

                     $operation->save();

                  }
               }

               $debug .= "}";

               Log::debug($debug);


            }
         }


      }
}
