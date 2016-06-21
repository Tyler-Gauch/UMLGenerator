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
			var umlclass = new UMLClass({});
			$("#class_"+umlclass.id).trigger({type: "mousedown", which:1});
			$("#class_"+umlclass.id).mouseup();
			$("#edit_classname").focus();
			$("#edit_classname").select();
			$("#select").click();
			$("#edit_form").removeClass("hidden");
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
			var c = UMLClasses[$("#edit_target").val()];
			if(c != null){
				$("#"+$("#edit_target").val()).removeClass("massmove");
			}
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

	$(document).on("mouseup", ".umlcanvas", function(e){
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
});