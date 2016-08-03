$(document).ready(function(){

		$("#save").on("click", function(){
			var content = "<svg>"+$(".umlcanvas").html()+"</svg>";
			  var blob = new Blob([content]);
			  var evt = document.createEvent("HTMLEvents");
			  evt.initEvent("click");
			  $("<a>", {
			    download: "url_generator.svg",
			    href: webkitURL.createObjectURL(blob)
			  }).get(0).dispatchEvent(evt);
		});

		$(".umlcanvas").on("contextmenu", function(e){
			if(status == "draw_line"){
				e.preventDefault();
				$("#line-temp").remove();
				delete holderObject["pathStart"];
				delete holderObject["pathEnd"];
			}
		});

		$(".add_class").on("click", function(e){
			e.preventDefault();
			addClass();
		});
		function addClass(){
			console.log("Adding new class");
			var umlclass = new UMLClass({});
			addClassToListView(umlclass);
			$("#class_"+umlclass.id).trigger({type: "mousedown", which:1});
			$("#class_"+umlclass.id).mouseup();
			$("#edit_classname").focus();
			$("#edit_classname").select();
			$("#select").click();
			$("#edit_form").removeClass("hidden");
			$("#list_view").addClass("hidden");
			$("#edit_line_form").addClass("hidden");

		}

		$(".btn-toolbar").on("click", function(){
			if($(this).hasClass("skip-status")){
				return;
			}
			if(status != null)
			{
				$("#"+status).removeClass("btn-warning");
				$("html, body").css("cursor", "");
				holderObject = {};
				// $("#edit_form").addClass("hidden");
			}
			status = $(this).attr("id");
			$(this).addClass("btn-warning");
		});

		$("#draw_line").on("click", function(){
			$("html, body").css("cursor", "crosshair");
			$(this).addClass("btn-warning");
		});

		$("#scale").on("click", function(){
			$("html, body").css("cursor", "ew-resize");
			$(this).addClass("btn-warning");
		});
	

	$(document).on("mousedown", ".umlclass", function(e){
		e.preventDefault();
		if(e.which == 1){
			console.log(status);
			if(status == "draw_line")
			{
				var c = UMLClasses[$(this).attr("id")];

				if(holderObject["pathStart"] == undefined)
				{
					var mouseClick = c.findClosestConnection(e.clientX - $(".umlcanvas").offset().left, e.clientY - $(".umlcanvas").offset().top);
					mouseClick["c"] = c;

					holderObject["pathStart"] = mouseClick;
				}else{
					$("#line-temp").remove();
					holderObject["pathStart"]["c"].addRelationship(c.className, "references");

					delete holderObject["pathStart"];
					delete holderObject["pathEnd"];
				}
			}
		}
	});

	$(".umlcanvas").on("mousemove", function(e){
		if(status == "draw_line" && holderObject["pathStart"] != undefined)
		{
			$("#line-temp").remove();
			var c = holderObject["pathStart"].c;
			var mouseClick = c.findClosestConnection(e.clientX - $(".umlcanvas").offset().left, e.clientY - $(".umlcanvas").offset().top);
			mouseClick["c"] = c;
			holderObject["pathStart"] = mouseClick;
			var path = '<svg height="5000" width="5000" id="line-temp">';
				path += "<path class='line temp' d='M"+holderObject["pathStart"].x+" "+holderObject["pathStart"].y+" L"+(e.clientX - $(".umlcanvas").offset().left-1)+" "+(e.clientY - $(".umlcanvas").offset().top-1) +"'></path>";
			path += '</svg>';
			$(".umlcanvas").append(path);
		}else if(status == "scale" && holderObject["scaling"] != undefined)
		{
			var newMouse = e.clientX;
			var startMouse = holderObject["startMouse"];
			var startViewBox = holderObject["startViewBox"];

			var newViewBox = startViewBox + (-1 * ((newMouse - startMouse) * 10)); //10 is the scaling factor

			if(newViewBox < 100)
			{
				newViewBox = 100;
			}

			$(".umlcanvas")[0].setAttribute("viewBox", "0 0 "+newViewBox+" "+newViewBox);
		}
	});

	$(document).on("click", ".umlcanvas", function(e){
		if(status == "select")
		{
			$("#edit_form").addClass("hidden");
			$("#edit_line_form").addClass("hidden");
			$("#list_view").removeClass("hidden");

			$(".umlclass.massmove").each(function(){
				UMLClasses[$(this).attr("id")]
					.unselect()
					.unhover()
					.getNode().removeClass("massmove");
			});
		}
	});

	$(document).on("mousedown", ".umlcanvas", function(e){
		if(status == "scale")
		{
			holderObject["startMouse"] = e.clientX;
			holderObject["startViewBox"] = parseInt($(".umlcanvas")[0].getAttribute("viewBox").split(" ")[2]);
			holderObject["scaling"] = true;
		}
	});

	$(document).on("mouseup", ".umlcanvas, .umlclass, document", function(e){
		if(status == "scale")
		{
			delete holderObject["startMouse"];
			delete holderObject["startViewBox"];
			delete holderObject["scaling"];
		}
	});

	$(document).on("keyup", function(e){
		e.preventDefault();
		e.stopPropagation();

		var keycode = (e.keyCode ? e.keyCode : e.which);

		if(keycode == 67 && e.ctrlKey && e.shiftKey)
		{
			addClass();
			return false;
		}
	});

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

		var type = $("#new_project_type").val();
		var url = $("#new_project_url").val();
		var projectName = $("#new_project_name").val();
		var id = $("#new_project_repo").val();
		var language = $("#new_project_language").val();
		var repoName = $("#new_project_repo").val();

		var postData = {type: type};

		if(type == "null")
		{
			return;
		}else if(type == "empty"){
			postData["projectName"] = projectName;
		}else if(type == "github"){
			if(repoName != "null")
			{
				postData["repoName"] = repoName;
				postData["language"] = language;
			}else{
				postData["repoUrl"] = url;
			}
		}

		$.ajax({
			method: "POST",
			url: "/project/create",
			data: postData,
			success: function(data){
				if(data.success)
				{
					if(type == "empty")
					{
						window.location = "/"+projectName;
					}else{
						window.location = "/"+repoName;
					}
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

	$(".edit_class_branch").on("click", function(e){
		e.preventDefault();
		window.showLoader();
		var t = $(this);
		$.ajax({
			url: "/parser/"+$("#project_name").data("project")+"/"+$(this).data("branch"),
			method: "GET",
			success: function(data){
				window.hideLoader();
				if(data.success)
				{
					$(".edit_class_branch").each(function(){
						$(this).children().each(function(){
							$(this).removeClass("fa-check");
						});
					});

					t.children().each(function(){
						$(this).addClass("fa-check");
					});

					$.each(data.data, function(key, value){
						var umlClass = new UMLClass(value);
					});

					$("#list_view").empty();					

					$.each(UMLClasses, function(id, umlClass){

						addClassToListView(umlClass);

						$.each(umlClass.relationships, function(type, relations){
							console.log(type, relations);
							$.each(relations, function(key, className){
								console.log(key, className);
								umlClass.addRelationship(className, type);
							});
						});
					});

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
		var c = UMLClasses[$(this).attr("id")];
		if(holderObject["hovering"] == undefined && !c.moving)
		{
			holderObject["hovering"] = true;
			c.hover();
		}
	});

	$(document).on("mouseleave", ".umlclass", function(e){
		delete holderObject["hovering"];
		var c = UMLClasses[$(this).attr("id")];
		if(!c.moving && !e.ctrlKey){
			c.unhover();
		}
	});
});