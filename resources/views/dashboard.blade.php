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
      	@if(Auth::user()->hasRole("USER"))
      	<li>
      		<a href="#" class="clear-fix open_project">
      			Open Project <span class="hot_key pull-right">CTRL+O</span>
      		</a>
      	</li>
	      	@if(isset($project))
	      	<li>
	      		<a href="#" id="save_project" class="clear-fix">
	      			Save <span class="hot_key pull-right">CTRL+S</span>
	      		</a>
	      	</li>
	      	<li>
	      		<a href="#" id="open_project_settings" class="clear-fix">
	      			Project Settings<span class="hot_key pull-right"></span>
	      		</a>
	      	</li>
	      	@endif
      	@endif
      	@if(isset($branches))
      		<li>
	      		<a href="#" id="force_github_load" class="clear-fix">
	      			Reload From GitHub<span class="hot_key pull-right"></span>
	      		</a>
      		</li>
      	@endif
      </ul>
    </li>
    @if(isset($project))
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
    @endif
@endsection

@section("content")
	<div class="row">
		<div class="col-lg-2">
			<div class="row">
				@if(isset($project))
					@if(isset($branches))
						<div class="dropdown">
						  	<button class="btn btn-block dropdown-toggle" type="button" id="project_name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-project="{{$project->name}}">
						    {{$project->name}}
						    	<span class="fa fa-chevron-circle-down"></span>
						  	</button>
						  	<ul class="dropdown-menu" id="edit_class_branch_list" aria-labelledby="project_name">
						    @foreach($branches as $key=>$branch)
						    	<li><a data-branch="{{ $branch->name }}" class="edit_class_branch">{{ $branch->name }} <span class="fa" style="color: #5CB85C"></span></a></li>
							@endforeach
							</ul>
						</div>
					@else
						<legend id="project_name"  data-project="{{$project->name}}">{{$project->name}}</legend>
					@endif

					<input type="hidden" id="current_branch" value="null"/>
				@endif
				<div class="col-lg-12">
				</div>
			</div>

			<div class="row" id="list_view" style="max-height: 80%; overflow-y:scroll">
				
			</div>

			<div class="row hidden" id="edit_line_form" style="max-height: 80%; overflow-y:scroll">
				<div class="col-lg-6">
					<button class="btn btn-success edit_back"><span class="fa fa-arrow-left"></span></button>
				</div>
				<div class="col-lg-6">
					<button id="edit_line_delete" class="btn btn-danger pull-right"><span class="fa fa-trash"></span></button>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Start Marker:</label>
						</div>
						<div class="row">
							<select id="edit_line_start" class="form-control allow-hotkeys">
								<option value="none">None</option>
								<option value="arrowLine">Arrow Line</option>
								<option value="arrowEmpty">Arrow</option>
								<option value="arrowFill">Arrow Filled</option>
								<option value="diamondEmpty">Diamond</option>
								<option value="diamondFill">Diamond Filled</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Line Type:</label>
						</div>
						<div class="row">
							<select id="edit_line_type" class="form-control allow-hotkeys">
								<option value="solid">Solid</option>
								<option value="dotted">Dotted</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>End Marker:</label>
						</div>
						<div class="row">
							<select id="edit_line_end" class="form-control allow-hotkeys">
								<option value="none">None</option>
								<option value="arrowLine">Arrow Line</option>
								<option value="arrowEmpty">Arrow</option>
								<option value="arrowFill">Arrow Filled</option>
								<option value="diamondEmpty">Diamond</option>
								<option value="diamondFill">Diamond Filled</option>
							</select>
						</div>
					</div>
				</div>
				<input type="hidden" id="edit_line_target"/>
			</div>

			<div class="row hidden" id="edit_form" style="max-height: 80%; overflow-y:scroll">
				<div class="col-lg-6">
					<button class="btn btn-success edit_back"><span class="fa fa-arrow-left"></span></button>
				</div>
				<div class="col-lg-6">
					<button id="edit_delete" class="btn btn-danger pull-right"><span class="fa fa-trash"></span></button>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Name:</label>
						</div>
						<div class="row">
							<input type="text" id="edit_classname" class="form-control allow-hotkeys"/>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Class Type: </label>
						</div>
						<div class="row">
							<select class="form-control allow-hotkeys" id="edit_class_type">
								<option value="class">Class</option>
								<option value="abstract">Abstract</option>
								<option value="interface">Interface</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Attributes:</label>
						</div>
						<div class="row" id="edit_attributes" style="max-height: 25%; overflow-y:scroll">

						</div>
						<div class="row" style="padding-top: 10px">
							<div class="col-lg-12">
								<div class="input-group">
									<input type="text" id="edit_attributes_add" class="form-control allow-hotkeys" placeholder="New Attribute Name"/>
									<span class="input-group-btn">
										<button class="btn btn-primary" id="edit_attributes_add_btn">+</button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<label>Functions:</label>
						</div>
						<div class="row" id="edit_functions" style="max-height: 25%; overflow-y:scroll">
						</div>
						<div class="row" style="padding-top: 10px">
							<div class="col-lg-12">
								<div class="input-group">
									<input type="text" id="edit_functions_add" class="form-control allow-hotkeys" placeholder="New Function Name"/>
									<span class="input-group-btn">
										<button class="btn btn-primary" id="edit_functions_add_btn">+</button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>	
				<input type="hidden" value="null" id="edit_target"/>
			</div>
		</div>
		@if(isset($project))
		<div class="col-lg-10">
			<div class="row parent-container" id="parent">
				<svg class="umlcanvas" preserveAspectRatio="xMinYMin meet" viewBox="0 0 5000 5000">
					<defs>
					    <marker id="diamondFill" markerWidth="10" markerHeight="10" refx="8" refy="3" orient="auto-start-reverse" markerUnits="strokeWidth">
					    	<path d="M-1,3 L4,6 L8,3 L4,0 z" class="marker"/>
					    </marker>
					    <marker id="diamondEmpty" markerWidth="12" markerHeight="12" refx="8" refy="3" orient="auto-start-reverse" markerUnits="strokeWidth">
					    	<path d="M0,3 L4,6 L8,3 L4,0 L0,3" class="marker empty"/>
					    </marker>
					    <marker id="arrowEmpty" markerWidth="12" markerHeight="12" refx="9" refy="3" orient="auto-start-reverse" markerUnits="strokeWidth">
					    	<path d="M0,0 L0,6 L9,3 z" class="marker empty"/>
					    </marker>
					    <marker id="arrowFill" markerWidth="12" markerHeight="12" refx="9" refy="3" orient="auto-start-reverse" markerUnits="strokeWidth">
					    	<path d="M0,0 L0,6 L9,3 z" class="marker"/>
					    </marker>
					    <marker id="arrowLine" markerWidth="12" markerHeight="12" refx="9" refy="3" orient="auto-start-reverse" markerUnits="strokeWidth">
					    	<path d="M0,6 L9,3 M0,0 L9,3" class="marker"/>
					    </marker>
					  </defs>
				</svg>
			</div>
		</div>
		@endif
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" id="new_project_modal"
		@if(!isset($project))
			 data-backdrop="static" data-keyboard="false" 
		@endif
	>
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	      	@if(isset($project))
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        @endif
	        <h4 class="modal-title">New Project</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
	      		<div class="col-lg-12">
	      			<div class="form-group">
	      				<div class="col-lg-4">
	      					<label>Project Type:</label>
	      				</div>
	      				<div class="col-lg-8">
	      					<select id="new_project_type" class="form-control allow-hotkeys">
	      						<option value="null">Please Choose a Project Type</option>
	      						<option value="empty">Empty Project</option>
	      						<option value="github">GitHub Project</option>
	      					</select>
	      				</div>
	      			</div>
	      		</div>
	      	</div>
	      	<div class="row hidden" id="new_empty_project">
	      		<div class="form-group">
		        	<div class="col-lg-12">

		        		<div class="col-lg-4">
	    	    			<label>Project Name:</label>
	        			</div>
	        			<div class="col-lg-8">
		        			<input type="text" id="new_project_name" class="form-control allow-hotkeys" />
	        			</div>		        		
	        		</div>
		        </div>
	      	</div>
	      	<div class="row hidden" id="new_git_project">
	      		@if(Auth::user()->hasRole("GUEST"))
	      			<div class="col-lg-12">
	      				<div class="alert alert-danger">If you would like to use a private repository from your list of repositories, please login!</div>
	      			</div>
	      		@endif
		        <div class="col-lg-12">
		        	<div class="form-group">
		        		<div class="col-lg-4">
		        			<label>Repository:</label>
		        		</div>
		        		<div class="col-lg-8">
		        			<div class="row">
		        				<div class="col-lg-12">
				        			<select id="new_project_repo" class="form-control allow-hotkeys">
				        			</select>
			        			</div>
			        			@if(Auth::user()->hasRole("USER"))
				        			<div class="col-lg-12" style="text-align: center">
				        			OR
				        			</div>
			        			@endif
		        				<div class="col-lg-12">
			        				<input type="text" id="new_project_url" class="form-control allow-hotkeys" placeholder="Public GitHub URL" />
		        				</div>
		        				<div class="col-lg-12">
		        					<input type="text" id="new_project_branch" class="form-control allow-hotkeys" placeholder="Public GitHub URL Branch"/>
		        				</div>
		        			</div>
		        		</div>		        		
		        	</div>
		        </div>
		        <div class="col-lg-12">
		        	<div class="col-lg-4">
		        		<label>Language</label>
		        	</div>
		        	<div class="col-lg-8">
		        		<select id="new_project_language" class="form-control allow-hotkeys">
		        			<option value="null">Please Select a Language</option>
		        			<option value="java">JAVA</option>
		        			<option value="php" disabled>PHP-Coming Soon</option>
		        		</select>
		        	</div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        @if(isset($project))
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	       @else
	       	<button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#initial_prompt">Back</button>
	       @endif
	        <button type="button" class="btn btn-primary hidden" id="new_project_create">Create Project</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" tabindex="-1" role="dialog" id="open_project_modal"
		@if(!isset($project))
			 data-backdrop="static" data-keyboard="false" 
		@endif
	>
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	      	@if(isset($project))
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        @endif
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
		        			<select id="open_project_name" class="form-control allow-hotkeys">
		        			</select>
		        		</div>
		        	</div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        @if(isset($project))
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	       @else
	       	<button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#initial_prompt">Back</button>
	       @endif
	        <button type="button" class="btn btn-primary" id="open_project_button">Open Project</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" tabindex="-1" role="dialog" id="project_settings">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Project Settings</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
		        <div class="col-lg-12">
		        	<div class="form-group">
		        		<div class="col-lg-4">
		        			<label>Project Name</label>
		        		</div>
		        		<div class="col-lg-8">
		        			<input type="text" class="form-control allow-hotkeys" id="project_settings_name"/>
		        		</div>
		        	</div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="project_settings_save">Open Project</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" tabindex="-1" role="dialog" id="initial_prompt" data-backdrop="static" data-keyboard="false" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">What would you like to do?</h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row">
	      		<div class="col-lg-offset-3 col-lg-3">
			        <div class="row">
		        		<button class="btn btn-prompt btn-default new_project" data-dismiss="modal"><span style="font-size: 64px" class="fa fa-file-text-o"></span></button>
		        	</div>
			        <div class="row">
		        		<strong>New Project</strong>
		        	</div>
		        </div>
		        <div class="col-lg-6">	
			        <div class="row">
			        	<button class="btn btn-prompt btn-default open_project" data-dismiss="modal"><span class="fa fa-folder-open-o" style="font-size: 64px"></span></button>
			        </div>
			        <div class="row">
	    	    		<strong>Open Project</strong>
	    	    	</div>
		        </div>
	        </div>
	        	
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div id="subtle_save_gear">
	 	<img src="gears.gif"/>Saving...
	</div>

@endsection

@section("javascript")
	<script>
		//global variables
		var UMLClasses = {};
		var i = 0;
		var status = "select";
		var holderObject = {};
		var viewBoxDefault = 5000;
		var defaultMarker = "arrowLine";
		var needsSave = false;
		var lastAction = new Date().getTime();
		var autoSaveTimeout = 2500; //inactivity for more than x milliseconds triggers an autosave
		@if(isset($project))
		var projectName = "{{ $project->name }}";
		var projectId = "{{ $project->id }}";
		UMLClassSaveURL = projectName+"/save";
		var loadFromUrl = @if($project->url == null) false; @else true; @endif;
		@else
			$("#initial_prompt").modal("show");
		@endif

		var userIsGuest = @if(Auth::user()->hasRole("GUEST")) true @else false @endif

		//blade variables

		//global functions
		window.distance = function(x1, y1, x2, y2)
		{
			return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
		}

		window.midpoint = function(x1, y1, x2, y2)
		{
			return {x: (parseFloat(x1)+parseFloat(x2))/2, y: (parseFloat(y1)+parseFloat(y2))/2};
		}

		function addClassToListView(umlClass){
			var lv = $("#list_view");
			var html = '<div class="col-lg-12 list_view_element" data-target="class_'+umlClass.id+'" id="list_view_class_'+umlClass.id+'">'+
							'<strong>'+umlClass.className+'</strong>'+
			'			</div>';

			lv.append(html);
		}

		function removeClassFromListView(umlClassId)
		{
			$("#list_view_class_"+umlClassId).remove();
		}

		function addClass(obj = {}){
			console.log("adding class", obj);
			var umlclass = new UMLClass(obj);
			addClassToListView(umlclass);
			$("#class_"+umlclass.id).trigger({type: "mousedown", which:1});
			$("#class_"+umlclass.id).mouseup();
			$("#edit_classname").focus();
			$("#edit_classname").select();
			$("#select").click();
			$("#edit_form").removeClass("hidden");
			$("#list_view").addClass("hidden");
			$("#edit_line_form").addClass("hidden");
			needsSave = true;
		}
		@if(isset($project))
			function loadProjectModels(branch = null, callback=function(){}){	
				var postData = {};
				if(branch != null)
				{
					postData["branch"] = branch;
				}

				$.ajax({
					data: postData,
					method: "POST",
					url: "/"+projectName+"/load",
					success: function(data){
						if(data.success)
						{
							console.log(data);
							$.each(data.model, function(key, umlClass){
								addClass(umlClass);
							});
						}
						callback(data.success);
					}
				});
			}
		@endif

		$(window).unload(function() {
		      alert('Handler for .unload() called.');
		});

		window.onbeforeunload = function(e) {
			if(needsSave)
			{
		  		return 'You have unsaved changes, are you sure you want to leave?';
		  	}
		};

		function save(beSubtle = false){
			@if(Auth::user()->hasRole("USER"))
				if(!needsSave)
					return;

				if(!beSubtle){
					window.showLoader("Please Wait.  Saving in Progess...");
				}else{
					$("#subtle_save_gear").css("display", "block");
				}
				UMLClassSaveChanged(window.hideLoader());
				delete holderObject["editedClasses"];
				delete holderObject["deletedClasses"];
				needsSave = false;
				if(beSubtle)
				{
					$("#subtle_save_gear").css("display", "none");
				}
			@endif
		}

		function setClassEdited(umlClass)
		{
			@if(Auth::user()->hasRole("USER"))
				if(holderObject["editedClasses"] == undefined)
				{
					holderObject["editedClasses"] = [];	
				}

				var exists = false;
				$.each(holderObject["editedClasses"], function(key, value){
					if(value.id == umlClass.id){
						exists = true;
						return;
					}
				});

				if(!exists)
					holderObject["editedClasses"].push(umlClass);
				
				needsSave = true;
				lastAction = new Date().getTime();
			@endif
		}
		function setClassDeleted(umlClass)
		{
			@if(Auth::user()->hasRole("USER"))
				if(holderObject["deletedClasses"] == undefined)
				{
					holderObject["deletedClasses"] = [];	
				}

				var exists = false;
				$.each(holderObject["deletedClasses"], function(key, value){
					if(value.id == umlClass.id){
						exists = true;
						return;
					}
				});

				if(!exists)
					holderObject["deletedClasses"].push(umlClass);
				
				needsSave = true;
				lastAction = new Date().getTime();
			@endif
		}

		@if(Auth::user()->hasRole("USER"))
			setInterval(function(){
				var currentTime = new Date().getTime();

				if(needsSave && currentTime - lastAction > autoSaveTimeout)
				{
					save(true);
				}
			}, 500);

			setInterval(function(){
				$(".list_view_element").find("span").remove();
				if(needsSave)
				{
					console.log("Needs save");
					$.each(holderObject["editedClasses"], function(key, umlClass){
						$("#list_view_class_"+umlClass.id).append("<span>*</span>");
					});
				}
			}, 100);
		@endif

	</script>

	<script src="/js/jquery-hotkeys.js"></script>
	<script src="/js/UMLClass.js"></script>
	<script src="/js/editForm.js"></script>
	<script src="/js/umlClassMovement.js"></script>
	<script src="/dashboard/dashboard.js"></script>
	<script src="/dashboard/hotkeys.js"></script>
	<script>

		$(document).ready(function(){

			$("#save_project").on("click", function(){
				save();
			});

			$(document).on("click", ".list_view_element", function(){
				$("#"+$(this).data("target")).trigger({type: "mousedown", which:1});
				$("#"+$(this).data("target")).mouseup();
			});

			@if(isset($project))
				@if(isset($branches))
					$("#edit_class_branch_list").find("a").click();
				@else
					loadProjectModels();
				@endif
			@endif

			$(document).on("click", ".line", function(e){
				e.preventDefault();
				e.stopPropagation();

				$("#list_view").addClass("hidden");
				$("#edit_form").addClass("hidden");
				$("#edit_line_form").removeClass("hidden");

				var path = $($(this).closest("path"));

				var startMarker = path.attr("marker-start");
				var endMarker = path.attr("marker-end");
				var dashArray = path.attr("stroke-dasharray");

				console.log(startMarker, endMarker, dashArray);

				if(startMarker == undefined)
				{
					$("#edit_line_start").val("none");
				}else {
					$("#edit_line_start").val(startMarker.replace("url(#", "").replace(")", ""));
				}

				if(endMarker == undefined)
				{
					$("#edit_line_end").val("none");
				}else {
					$("#edit_line_end").val(endMarker.replace("url(#", "").replace(")", ""));
				}

				if(dashArray == undefined)
				{
					$("#edit_line_type").val("solid");
				}else{
					$("#edit_line_type").val("dotted");
				}

				$("#edit_line_target").val(path.attr("id"));
			});

			$("#edit_line_start").on("change", function(){
				if($(this).val() == "none")
				{
					$("#"+$("#edit_line_target").val()).attr("marker-start", "");
				}else{
					$("#"+$("#edit_line_target").val()).attr("marker-start", "url(#"+$(this).val()+")");
				}
			});

			$("#edit_line_type").on("change", function(){
				if($(this).val() == "solid")
				{
					$("#"+$("#edit_line_target").val()).attr("stroke-dasharray", "");
				}else{
					$("#"+$("#edit_line_target").val()).attr("stroke-dasharray", "5,5");
				}
			});

			$("#edit_line_end").on("change", function(){
				if($(this).val() == "none")
				{
					$("#"+$("#edit_line_target").val()).attr("marker-end", "");
				}else{
					$("#"+$("#edit_line_target").val()).attr("marker-end", "url(#"+$(this).val()+")");
				}
			});

		});

	</script>
@endsection