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
@endsection