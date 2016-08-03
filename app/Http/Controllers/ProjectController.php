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
use App\Models\ModelObj;
use App\Models\ClassObj;
use App\Models\Attribute;
use App\Models\Operation;
use Carbon\Carbon;
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

         // Get the current timestamp which will be used for removing old attributes, functions, and classes later on
         $currentTime = Carbon::now()->toDateTimeString();
         
         // Get the project id of the current project and add the model
         $proj = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();
         $branch = $request->input("branch", null);

         // Get the model of the github branch or NULL if empty project
         $model = ModelObj::where("project_id", "=", $proj["id"])->where("branch", "=", $branch)->firstOrCreate(
            [
            "branch" => $branch, 
            "project_id" => $proj["id"]
            ]
         );

         // Loop through classes from user input
         $data = $request->all();
         foreach ($data as $curObj) {
            $className  = $curObj["className"];
            $locX       = $curObj["x"];  // starting x coordinate of the class 
            $locY       = $curObj["y"];  // starting y coordinate of the class
            $classType  = $curObj["type"];

            // Create the class in the database
            $curClass = ClassObj::where("name", "=", $className)->where("model_id", "=", $model->id)->firstOrNew(
               [
               "name"      => $className,
               "model_id"  => $model->id
               ]
            );

            // Update the values
            $curClass->locationX = $locX;
            $curClass->locationY = $locY;
            $curClass->type      = $classType;
            $curClass->updated_at= $currentTime; 

            // TODO Do we need the package name if its java?

            $curClass->save();


            // Get attribute and function names
            if (isset($curObj["attributes"])) {
               foreach ($curObj["attributes"] as $attr) {

                  // Get values from current attribute object
                  $attribute = Attribute::where("name", "=", $attr["name"])->where("class_id", "=", $curClass->id)->firstOrNew(
                     [
                     "name"      => $attr["name"],
                     "class_id"  => $curClass->id
                     ]
                  );

                  // Update the values
                  $attribute->visibility  = $attr["visibility"];
                  $attribute->type        = $attr["type"];
                  $attribute->is_static   = $attr["isStatic"];
                  $attribute->is_final    = $attr["isFinal"];
                  $attribute->is_abstract = $attr["isAbstract"];
                  $attribute->updated_at  = $currentTime; 

                  // TODO default value doesnt show up right, has a colon

                  $attribute->save();

               }

               // Now all attributes saved from the user have been updated. Remove any old attributes of the current class
               Attribute::where("class_id", "=", $curClass->id)->where("updated_at", "<", $currentTime)->delete();
            }


            if (isset($curObj["functions"])) {
               foreach ($curObj["functions"] as $func) {

                  // Get values from current function object
                  $operation = Operation::where("name", "=", $func["name"])->where("class_id", "=", $curClass->id)->firstOrNew(
                     [
                     "name"      => $func["name"],
                     "class_id"  => $curClass->id
                     ]
                  );

                  // Update the values
                  $operation->visibility  = $func["visibility"];
                  $operation->return_type = $func["type"];
                  $operation->is_static   = $func["isStatic"];
                  $operation->is_final    = $func["isFinal"];
                  $operation->is_abstract = $func["isAbstract"];
                  $operation->parameters  = $func["parameters"];
                  $operation->updated_at  = $currentTime; 

                  $operation->save();

               }

               // Now all functions saved from the user have been updated. Remove any old functions of the current class
               Operation::where("class_id", "=", $curClass->id)->where("updated_at", "<", $currentTime)->delete();
            }



         }

         // Now all classes saved from the user have been updated. Remove any old classes of the current model
         $oldClasses = ClassObj::where("model_id", "=", $model->id)->where("updated_at", "<", $currentTime)->get();
         
         foreach ($oldClasses as $oldClass) {

            // Ensure the children are removed
            $oldClass->Attribute()->delete();
            $oldClass->Operation()->delete();
            $oldClass->delete();
         }
      }

   
}
