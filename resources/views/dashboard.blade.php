@extends("base")

@section("stylesheets")
	<link rel="stylesheet" href="dashboard/dashboard.css"/>
@endsection

@section("extra_nav")
	<li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">File<span class="caret"></span></a>
      <ul class="dropdown-menu dropdown-menu-large">
      	<li>
      		<a href="#" class="clear-fix new_project">
      			New Project <span class="hot_key pull-right">CTRL+N</span>
      		</a>
      	</li>
      	<li>
      		<a href="#" class="clear-fix open_project">
      			Open Project <span class="hot_key pull-right">CTRL+O</span>
      		</a>
      	</li>
      	<li>
      		<a href="#" id="save_project" class="clear-fix">
      			Save <span class="hot_key pull-right">CTRL+S</span>
      		</a>
      	</li>
      	<li>
      		<a href="#" id="save_project_as" class="clear-fix">
      			Save As <span class="hot_key pull-right">CTRL+SHIFT+S</span>
      		</a>
      	</li>
      </ul>
    </li>
	<li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Edit<span class="caret"></span></a>
      <ul class="dropdown-menu dropdown-menu-large">
      	<li class="dropdown-submenu">
		    <a tabindex="-1" href="#">Add Object</a>
		    <ul class="dropdown-menu">
		      	<li>
		      		<a href="#" class="add_class">
		      			Add Class
		      		</a>
		      	</li>
		    </ul>
		  </li>
      	<li>
      		<a href="#" id="save">
      			Download <span class="hot_key pull-right">CTRL+D</span>
      		</a>
      	</li>
      </ul>
    </li>
    <button id="select" class="btn btn-primary btn-toolbar btn-warning navbar-btn" type="button"><i class="fa fa-mouse-pointer"></i></button>
    <button id="draw_line" class="btn btn-primary btn-toolbar navbar-btn"><i class="fa fa-arrows-h"></i></button>
    <button id="scale" class="btn btn-primary btn-toolbar navbar-btn"><i class="fa fa-arrows"></i></button>
@endsection

@section("content")
	<div class="row">
		<div class="col-lg-2">
			<div class="row">
				@if(isset($project))
					<legend id="project_name">{{$project->name}}</legend>
					<div class="col-lg-12">
						<select id="change_project_branch" class="form-control">
							<option value="null" selected>Please Choose A Branch</option>
							@foreach($branches as $key=>$branch)
								<option value="{{ $branch->name }}">{{$branch->name}}</option>
							@endforeach
						</select>
					</div>
				@endif
				<div class="col-lg-12">
				</div>
			</div>

			<div class="row hidden" id="edit_form">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Name:</label>
						</div>
						<div class="row">
							<input type="text" id="edit_classname" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Attributes:</label>
						</div>
						<div class="row" id="edit_attributes" style="height: 25%; overflow:scroll">
						</div>
						<div class="row">
							<div class="input-group">
								<input type="text" id="edit_attributes_add" class="form-control"/>
								<span class="input-group-btn">
									<button class="btn btn-primary" id="edit_attributes_add_btn">+</button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Functions:</label>
						</div>
						<div class="row" id="edit_functions" style="height: 25%; overflow:scroll">
						</div>
						<div class="row">
							<div class="input-group">
								<input type="text" id="edit_functions_add" class="form-control"/>
								<span class="input-group-btn">
									<button class="btn btn-primary" id="edit_functions_add_btn">+</button>
								</span>
							</div>
						</div>
					</div>
				</div>	
				<div class="col-lg-12">
					<button id="edit_delete" class="btn btn-danger">Delete Class</button>
				</div>
				<input type="hidden" value="null" id="edit_target"/>
			</div>
		</div>
		<div class="col-lg-10">
			<div class="row parent-container" id="parent">
				<svg class="umlcanvas" preserveAspectRatio="xMinYMin meet" viewBox="0 0 5000 5000">
				</svg>
			</div>
		</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" id="new_project_modal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">New Project</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
		        <div class="col-lg-12">
		        	<div class="form-group">
		        		<div class="col-lg-4">
		        			<label>Repository</label>
		        		</div>
		        		<div class="col-lg-8">
		        			<select id="new_project_repo" class="form-control">
		        			</select>
		        		</div>
		        	</div>
		        </div>
		        <div class="col-lg-12">
		        	<div class="col-lg-4">
		        		<label>Language</label>
		        	</div>
		        	<div class="col-lg-8">
		        		<select id="new_project_language" class="form-control">
		        			<option value="null">Please Select a Language</option>
		        			<option value="java">JAVA</option>
		        			<option value="php" disabled>PHP-Coming Soon</option>
		        		</select>
		        	</div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="new_project_create">Create Project</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" tabindex="-1" role="dialog" id="open_project_modal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Open Project</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
		        <div class="col-lg-12">
		        	<div class="form-group">
		        		<div class="col-lg-4">
		        			<label>Project Name</label>
		        		</div>
		        		<div class="col-lg-8">
		        			<select id="open_project_name" class="form-control">
		        			</select>
		        		</div>
		        	</div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="open_project_button">Open Project</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

@endsection

@section("javascript")
	<script>
		//global variables
		var UMLClasses = {};
		var i = 0;
		var status = "select";
		var holderObject = {};
		var viewBoxDefault = 5000;

		//blade variables

		//global functions
		window.distance = function(x1, y1, x2, y2)
		{
			return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
		}

		window.midpoint = function(x1, y1, x2, y2)
		{
			return {x: (x1+x2)/2, y: (y1+y2)/2}
		}

	</script>

	<script src="/js/UMLClass.js"></script>
	<script src="/js/editForm.js"></script>
	<script src="/js/umlClassMovement.js"></script>
	<script src="/dashboard/dashboard.js"></script>
	<script>
		$(document).ready(function(){

			$(".new_project").on("click", function(e){
				e.preventDefault();

				getRepoNames(function(data){
					if(data.success)
					{
						var modal = $("#new_project_modal");
						var repoList = $("#new_project_repo");

						repoList.empty();
						repoList.append("<option value='null'>Please Select a Repository</option>");

						$.each(data.repos, function(key, value){
							repoList.append("<option value='"+value+"'>"+value+"</option>");
						});

						modal.modal("show");
					}
				});
				
			});

			$("#new_project_create").on("click", function(e){
				e.preventDefault();

				var id = $("#new_project_repo").val();
				var language = $("#new_project_language").val();
				var name = $("#new_project_repo").val();

				$.ajax({
					method: "POST",
					url: "/project/create",
					data: {name: name, language: language},
					success: function(data){
						if(data.success)
						{
							window.location = "/"+name;
						}else{
							alert("Error:"+data.message);
						}
					}
				});
			});

			function getRepoNames(callback){
				$.ajax({
					method:"POST",
					url: "/repo/list",
					success: function(data){
						callback(data);
					}
				});
			}

			function getBranchNames(callback)
			{
				$.ajax({
					method: "POST",
					url: "/branch/list",
					data: {repo: repo},
					success: function(data){
						callback(data);
					}
				});
			}

			function getProjects(callback){
				$.ajax({
					method: "GET",
					url: "/project/get",
					success: function(data){
						callback(data);
					}
				});
			}

			$("#change_project_branch").on("change", function(e){
				$.ajax({
					url: "/parser/"+$("#project_name").text()+"/"+$(this).val(),
					method: "GET",
					success: function(data){
						if(data.success)
						{
							$.each(data.data, function(key, value){
								var umlClass = new UMLClass(value);
							});

							$.each(UMLClasses, function(id, umlClass){
								$.each(umlClass.relationships, function(key, className){
									umlClass.addRelationship(className);
								});
							});

							autoAlignClasses();

						}else{
							alert("Error: "+data.message);
						}
					}
				});
			});

			$(".open_project").on("click", function(e){
				e.preventDefault();

				getProjects(function(data){
					if(data.success){
						var modal = $("#open_project_modal");
						var projects = $("#open_project_name");

						projects.empty();
						projects.append("<option value='null'>Please select a Project</option>");

						$.each(data.projects, function(key, value){
							projects.append("<option value='"+value.name+"'>"+value.name+"</option>");
						});

						modal.modal("show");
					}
				});
			});

			$("#open_project_button").on("click", function(e){
				e.preventDefault();

				var project = $("#open_project_name").val();

				if(project == null || project == "null")
				{
					alert("Please select a project");
				}else{
					window.location = "/"+project;
				}
			});

			$(document).on("selectstart", "rect text", false);

			$(document).on("mouseover", ".umlclass", function(e){
				if(holderObject["hovering"] == undefined)
				{
					holderObject["hovering"] = true;
					var c = UMLClasses[$(this).attr("id")];
					c.render(c.selected, false, true);
				}
			});

			$(document).on("mouseleave", ".umlclass", function(e){
				delete holderObject["hovering"];
				var c = UMLClasses[$(this).attr("id")];
				if(!c.moving && !e.ctrlKey)
					c.render(c.selected);
			});

			function autoAlignClasses(){

				console.log("Auto Aligning Classes");

				var relationships = {};

				var relationshipsLength = 0;
				var totalRelationships = 0;

				$.each(UMLClasses, function(id, umlClass){

					var index = umlClass.relationships.length
					if(relationships[index] == undefined){
						relationships[index] = [];
						relationshipsLength++;
					}

					totalRelationships += index;
					relationships[index].push(umlClass); 

				});

				console.log(relationships);

				//we can center the largest one in the middle of the screen

				while(relationshipsLength > 0)
				{
					var maxKey = -1;
					for(var relationshipCount in relationships)
					{
						relationshipCount = parseInt(relationshipCount);
						if(relationshipCount > maxKey)
						{
							maxKey = relationshipCount;
						}
					}

					var current = relationships[maxKey];
					delete relationships[maxKey];
					relationshipsLength--;
					
					$.each(current, function(key, umlClass){

						

					});
				}

			}
		});
	</script>
@endsection