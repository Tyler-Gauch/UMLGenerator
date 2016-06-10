@extends("base")

@section("stylesheets")
	<style>
		.parent-container{
			width: 100%;
			height: 75%;
			border: 1px solid;
			overflow: scroll;
		}
		.umlcanvas{
			width: 5000px;
			height: 5000px;
		}
		.umlclass{
			border: 5px solid;
			display: inline-block;
			border-radius: 10px;
			fill: #FFFFFF;
			stroke-width: 1px;
			stroke: #000;
		}

		.umlclass-attributes-rect{
			fill: #FF7144;
			stroke-width: 1px;
			stroke: #000;
		}
		.umlclass-attributes-text{
			fill: black;
			stroke-width: 0px;
		}

		.umlclass-functions-rect{
			fill: #FF7144;
			stroke-width: 1px;
			stroke: #000;
		}
		.umlclass-functions-text{
			fill: black;
			stroke-width: 0px;
		}

		.umlclass-name-rect{
			fill: #AA5439;
		}
		.umlclass-name-text{
			text-anchor: middle;
			fill: black;
			stroke-width: 0px;
		}
		
		.selected{
			stroke-width: 5px;
			stroke: #00F;
		}

		#edit_form{
			width: 100%;
			overflow: scroll;
		}

		.toolbar{
			position: fixed;
			padding-left: 10px;
			padding-right: 10px;
			padding-top: 5px;
			padding-bottom: 5px;
			left:0px;
			top: 0px;
			width: 100%;
			height: 50px;
			background-color: #262121;
		}

		.btn-toolbar{
			width:30px !important;
			padding-left: 8px;
			border: 1px #CCC solid;
			display: block;
			height: 100%;
			border-radius: 0px !important;
			float:left;
		}

		.content{
			width:100%;
			padding-top: 55px;		
			padding-left: 25px;	
		}
		.brand{
			font-size: 32px !important;
			height:100%;
			display: block;
			float:left;
			padding-right: 20px;
			border-right: 1px #ccc solid;
			text-decoration: none !important;
			color: #ccc !important;
		}

		.line{
			stroke: black;
			stroke-width: 2px;
		}
		.line2{
			stroke-width: 2px;
		}

		.line:hover{
			stroke: red;
			stroke-width: 5px;
		}

		.line.temp{
			stroke: blue;
		}


	</style>
@endsection

@section("content")
	<div class="toolbar">
		<a class="brand" href="/">
			UML Generator
		</a>
		<button id="select" class="btn btn-primary btn-toolbar btn-warning"><i class="fa fa-mouse-pointer"></i></button>
		<button id="add_class" class="btn btn-primary btn-toolbar skip-status"><i class="fa fa-plus"></i></button>
		<button id="draw_line" class="btn btn-primary btn-toolbar"><i class="fa fa-arrows-h"></i></button>
		<button id="save" class="btn btn-primary btn-toolbar skip-status"><i class="fa fa-save"></i></button>
	</div>
	<div class="content">
		<div class="row parent-container" id="parent">
			<svg class="umlcanvas">
			</svg>
		</div>
		<div class="row hidden" id="edit_form">
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<label>Name:</label>
					</div>
					<div class="row">
						<input type="text" id="edit_classname" class="form-control"/>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<label>Attributes:</label>
					</div>
					<div class="row" id="edit_attributes">
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
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<label>Functions:</label>
					</div>
					<div class="row" id="edit_functions">
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

@endsection

@section("javascript")
	<script>
		//global variables
		var UMLClasses = {};
		var i = 0;
		var status = "select";
		var holderObject = {};

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
	<script>
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

			$("#add_class").on("click", function(){
				if(confirm("Would you like to add from a file?")){
					var filePath = prompt("Please Enter the file Path:");

					$.ajax({
						url: "/parser/java",
						method: "GET",
						data: {fileName: filePath},
						success: function(data){
							if(data.success){
								$.each(data.data, function(key, value){
									var umlclass = new UMLClass({className:value.className, attributes: value.attributes, functions: value.functions});
									$("#class_"+umlclass.id).trigger({type: "mousedown", which:1});
									$("#class_"+umlclass.id).mouseup();
									$("#edit_classname").focus();
									$("#edit_classname").select();
									$("#select").click();
									$("#edit_form").removeClass("hidden");
								});
							}else{
								alert("Error: "+data.message);
							}
						}
					});
				}else{
					var umlclass = new UMLClass({});
					$("#class_"+umlclass.id).trigger({type: "mousedown", which:1});
					$("#class_"+umlclass.id).mouseup();
					$("#edit_classname").focus();
					$("#edit_classname").select();
					$("#select").click();
					$("#edit_form").removeClass("hidden");
				}
			});

			$(".btn-toolbar").on("click", function(){
				if($(this).hasClass("skip-status")){
					return;
				}
				if(status != null)
				{
					$("#"+status).removeClass("btn-warning");
					$("html, body").css("cursor", "");
					holderObject = {};
					$("#edit_form").addClass("hidden");
				}
				status = $(this).attr("id");
				$(this).addClass("btn-warning");
			});

			$("#draw_line").on("click", function(){
				$("html, body").css("cursor", "crosshair");
				$(this).addClass("btn-warning");
			});
		

		$(document).on("mousedown", ".umlclass", function(e){
			if(status != "draw_line"){
				return;
			}
			e.preventDefault();
			if(e.which == 1){
				var c = UMLClasses[$(this).attr("id")];

				if(holderObject["pathStart"] == undefined)
				{
					var mouseClick = c.findClosestConnection(e.clientX - $(".umlcanvas").offset().left, e.clientY - $(".umlcanvas").offset().top);
					mouseClick["c"] = c;

					holderObject["pathStart"] = mouseClick;
				}else{
					var mouseClick = c.findClosestConnection(holderObject["pathStart"].x, holderObject["pathStart"].y);
					mouseClick["c"] = c;

					holderObject["pathEnd"] = mouseClick;
					$("#line-temp").remove();
					var path = '<svg height="5000" width="5000" data-start="class_'+holderObject["pathStart"].c.id+'" data-end="class_'+holderObject["pathEnd"].c.id+'">';
						path += "<path class='line' stroke-width='2px' stroke='black' d='M"+holderObject["pathStart"].x+" "+holderObject["pathStart"].y+" L"+holderObject["pathEnd"].x+" "+holderObject["pathEnd"].y+"'></path>";
					path += '</svg>';
			
					$(".umlcanvas").append(path);
					delete holderObject["pathStart"];
					delete holderObject["pathEnd"];
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
			}
		});

		$(".umlcanvas").on("click", function(){
			if(status == "select")
			{
				$("#edit_form").addClass("hidden");
				UMLClasses[$("#edit_target").val()].render();
			}
		});
	});
	</script>
@endsection