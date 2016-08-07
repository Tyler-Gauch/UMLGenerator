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
use App\Helpers\ProjectHelper;

class ProjectController extends Controller
{
   	public function create(Request $request)
   	{
         $type = $request->input("type", null);
         $name = null;
         $language = "java";
         $url = null;
         $project_type_id = null;
         $repo = null;

         if($type == "empty")
         {
            $name = $request->input("projectName", null);
            if($name == null || $name == "null" || $name == "")
            {
               return response()->json(["success" => false, "message" => "You must choose a Project Name"]);
            }
            $project_type_id = ProjectType::where("name", "=", "empty")->first()->id;

         }else if($type == "github")
         {          
            $url = $request->input("url", null);
            $name = $request->input("repoName", null);
            $language = $request->input("language", null);

            if($url != null && $url != "null" && $url != "")
            {
               $branch = $request->input("branch", "master");
               $result = GitHubHelper::downloadPublicRepo($url, $branch);

               if(!$result["success"])
               {
                  return response()->json(["success" => false, "message" => $result["message"]]);
               }
               //if we got here url was right so we can extract the repo name to create the project
               $name = str_replace("https://github.com/", "", $url);
               $name = str_replace("/", "-", $name);
            }else{
               $repo = $name;
            }

            if($name == null || $name == "null" || $name == "")
            {
               if(Auth::user()->hasRole("GUEST"))
               {
                  return response()->json(["success" => false, "message" => "You must enter a URL"]);   
               }else{
                  return response()->json(["success" => false, "message" => "You must choose a Repository or enter a URL"]);
               }
            }
            
            if($language == null || $language == "null" || $language == "")
            {
               return response()->json(["success" => false, "message" => "You must choose a Language"]);
            }

         }

         if($type != "empty" && ($language == null || $language == "null" || $language == ""))
         {
            return response()->json(["success" => false, "message" => "You must choose a Project Type"]);
         }

         Log::info("$name, $repo, $language, $url");

         $project = Project::where("name", $name)->where("user_id", Auth::user()->id)->firstOrCreate([
            "name" => $name,
            "repo" => $repo,
            "language_id" => Language::where("name", "=", $language)->first()->id,
            "user_id" => Auth::user()->id,
            "project_type_id" => ProjectType::where("name", "=", $type)->first()->id,
            "url" => $url
         ]);

   		return response()->json(["success" => true, "projectName" => $name]);
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

      public function save(Request $request, $project, $branch = null) {
         
         // Get the current timestamp which will be used for removing old attributes, functions, and classes later on
         $currentTime = Carbon::now()->toDateTimeString();
         
         // Get the project id of the current project and add the model
         $proj = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();

         // Get the model of the github branch or NULL if empty project
         $model = ModelObj::where("project_id", "=", $proj["id"])->where("branch", "=", $branch)->firstOrCreate(
            [
            "branch" => $branch, 
            "project_id" => $proj["id"]
            ]
         );

         $savedItems = $request->input("savedItems", "{}");

         $savedItems = \json_decode($savedItems, true);
         // Loop through classes from user input
         foreach ($savedItems as $curObj) {
            Log::info("Processing Class: {$curObj['className']}");
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
                  $attribute->visibility  = ( isset($attr["visibility"]) ? $attr["visibility"] : "public");
                  $attribute->type = ( isset($attr["type"]) ? $attr["type"] : ""); //constructors have no type
                  $attribute->is_static   = ( isset($attr["isStatic"]) ? $attr["isStatic"] : false);
                  $attribute->is_final    = ( isset($attr["isFinal"]) ? $attr["isFinal"] : false);
                  $attribute->is_abstract = ( isset($attr["isAbstract"]) ? $attr["isAbstract"] : false);
                  $attribute->default_value = ( isset($attr["default"]) ? $attr["default"] : "");
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


                  // Log::info(print_r($func, 1));

                  // Update the values
                  //for some reason some values are getting lost when sent form the front end
                  $operation->visibility  = ( isset($func["visibility"]) ? $func["visibility"] : "public");
                  $operation->return_type = ( isset($func["type"]) ? $func["type"] : ""); //constructors have no type
                  $operation->is_static   = ( isset($func["isStatic"]) ? $func["isStatic"] : false);
                  $operation->is_final    = ( isset($func["isFinal"]) ? $func["isFinal"] : false);
                  $operation->is_abstract = ( isset($func["isAbstract"]) ? $func["isAbstract"] : false);
                  $operation->parameters  = ( isset($func["parameters"]) ? $func["parameters"] : "()");
                  $operation->updated_at  = $currentTime;

                  $operation->save();

               }

               // Now all functions saved from the user have been updated. Remove any old functions of the current class
               Operation::where("class_id", "=", $curClass->id)->where("updated_at", "<", $currentTime)->delete();
            }
         }

         // Now all classes saved from the user have been updated. Remove any old classes of the current model
         $deletedClasses = $request->input("deletedClasses", "{}");
         $deletedClasses = \json_decode($deletedClasses, true);

         
         foreach ($deletedClasses as $oldClass) {
            echo "Processing Class: $oldClass\n"; 
            $curClass = ClassObj::where("name", "=", $oldClass)->where("model_id", "=", $model->id)->first();
            if($curClass == null)
            {
               echo "\tClass Not Found";
               continue;
            }

            // Ensure the children are removed
            $curClass->Attributes()->delete();
            $curClass->Operations()->delete();
            $curClass->delete();
         }
      }


      public function load(Request $request, $project = null)
      {
         if($project == null)
         {
            return response()->json(["success" => false, "message" => "You must provide a project"]);
         }

         $project = Project::where("name", "=", $project)->where("user_id", "=", Auth::user()->id)->firstOrFail();

         $branch = $request->input("branch", null);

         
         $model = null;

         if($branch == null)
         {
            $model = $project->Models()->first();
         }else{
            $model = $project->Models()->where("branch", "=", $branch)->first();            
         }

         if($model == null)
         {
            return response()->json(["success" => false, "message" => "We were unable to get your model"]);
         }

         //get all project information and build required data structures
         $m = [];

         foreach($model->Classes()->get() as $class){
            $c = [];
            $c["className"] = $class->name;
            $c["x"] = $class->locationX;
            $c["y"] = $class->locationY;
            $c["classType"] = $class->type;
            $c["package"] = $class->package;
            $c["attributes"] = [];
            $c["functions"] = [];
            $c["relationships"] = [];
            $c["dbId"] = $class->id;

            foreach($class->Attributes()->get() as $attribute)
            {  
               $a = [];
               $a["name"] = $attribute->name;
               $a["visibility"] = $attribute->visibility;
               $a["type"] = $attribute->type;
               $a["default"] = $attribute->default_value;
               $a["isStatic"] = $attribute->is_static;
               $a["isFinal"] = $attribute->is_final;
               $a["isabstract"] = $attribute->is_abstract;
               $c["attributes"][] = $a;

            }
            foreach($class->Operations()->get() as $operation)
            {
               $o = [];
               $o["name"] = $operation->name;
               $o["visibility"] = $operation->visibility;
               $o["type"] = $operation->type;
               $o["parameters"] = $operation->parameters;
               $o["isStatic"] = $operation->is_static;
               $o["isFinal"] = $operation->is_final;
               $o["isabstract"] = $operation->is_abstract;
               $c["functions"][] = $o;
            }

            $m[] = $c;
         }
         
         $data = [];
         $data["success"] = true;
         $data["model"] = $m;

         return response()->json($data);
      }
   
}
