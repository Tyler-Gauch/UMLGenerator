var currentX = null;
var currentY = null;

$(document).on("mousedown touchstart", ".umlclass", function(e)
{
	if(status != "select")
	{
		return;
	}
	if(e.which == 1)
	{
		if($("#edit_target").val() != "null" && !e.ctrlKey){
			// UMLClasses[$("#edit_target").val()].render();
			$("#rect_hover_"+$("#edit_target").val()).removeClass("selected");
			$("#rect_hover_"+$("#edit_target").val()).removeClass("hover");
		}
		else if(e.ctrlKey)
		{
			$("#edit_target").val("null");
		}

		var c = UMLClasses[$(this).attr("id")];
		
		$(this).addClass("massmove");
		$("#rect_hover_"+$(this).attr("id")).addClass("selected");

		currentX = e.clientX;
		currentY = e.clientY;
		
		var currentMatrix = $(this).attr("transform").slice(7,-1).split(" ");
		for(var i = 0; i < currentMatrix.length; i++)
		{
			currentMatrix[i] = parseFloat(currentMatrix[i]);
		}
		if(holderObject["currentMatrix"] == undefined || holderObject["currentMatrix"] == null)
			holderObject["currentMatrix"] = {};
		
		holderObject["currentMatrix"][$(this).attr("id")] = currentMatrix;

		if(e.ctrlKey)
		{
			$(".umlclass.massmove").each(function(){
				var c = UMLClasses[$(this).attr("id")];
				c.moving = true;
				var currentMatrix = $(this).attr("transform").slice(7,-1).split(" ");
				for(var i = 0; i < currentMatrix.length; i++)
				{
					currentMatrix[i] = parseFloat(currentMatrix[i]);
				}
				if(holderObject["currentMatrix"] == undefined || holderObject["currentMatrix"] == null)
					holderObject["currentMatrix"] = {};
				
				holderObject["currentMatrix"][$(this).attr("id")] = currentMatrix;
				console.log(holderObject);
			});
		}

		if(!e.ctrlKey){
			var id = $(this).attr("id");
			$(".umlclass.massmove").each(function(){
				if($(this).attr("id") == id)
				{
					return;
				}
				var c = UMLClasses[$(this).attr("id")];
				c.moving = false;
				$(this).removeClass("massmove");
				
			});
			$("#edit_classname").val(c.className);
			$("#edit_target").val($(this).attr("id"));

			var attrs = $("#edit_attributes");
			attrs.empty();
			$.each(c.attributes, function(key, value){appendEditAttribute(key, value)});

			var funcs = $("#edit_functions");
			funcs.empty();
			$.each(c.functions, function(key, value){appendEditFunction(key, value)});

			$("#edit_form").removeClass("hidden");
		}else{
			$("#edit_form").addClass("hidden");
		}
	}
});

$(document).on("mousemove touchmove", function(e){
	$(".umlclass.massmove").each(function(){
		var viewBoxValue = parseInt($(".umlcanvas")[0].getAttribute("viewBox").split(" ")[2]);
		var dx = (e.clientX - currentX) * (viewBoxValue/viewBoxDefault);
		var dy = (e.clientY - currentY) * (viewBoxValue/viewBoxDefault);

		var currentMatrix = holderObject["currentMatrix"][$(this).attr("id")];
		currentMatrix[4] += dx;
		currentMatrix[5] += dy;
		var c = UMLClasses[$(this).attr("id")];
		c.x = currentMatrix[4];
		c.y = currentMatrix[5];
		var newMatrix = "matrix("+currentMatrix.join(" ") + ")";
		$(this).attr('transform', newMatrix);
		

		$(document).find("[data-end='class_"+c.id+"']").each(function(key){
			moveRelationShip($(this));
		});
		$(document).find("[data-start='class_"+c.id+"']").each(function(key){
			moveRelationShip($(this));
		});
	});
	currentX = e.clientX;
	currentY = e.clientY;
});

$(document).on("mouseup touchend", ".umlclass", function(e){
	delete holderObject["currentMatrix"];
	e.stopPropagation();
	$(".umlclass.massmove").each(function(){
		if(!e.ctrlKey)
		{
			var c = UMLClasses[$(this).attr("id")];
			c.moving = false;
			$(this).removeClass("massmove");
		}
	});
});

$(document).on("click", ".umlclass", function(e){
	e.stopPropagation(); //this is to stop the hiding of the edit_form on mouseup
});

function moveRelationShip($this){
	var cStart = UMLClasses[$this.data("start")];
	var cEnd = UMLClasses[$this.data("end")];

	var emp = cEnd.midPoint();
	var smp = cStart.midPoint();
	var startPoint = cStart.findClosestConnection(emp.x, emp.y);
	var endPoint = cEnd.findClosestConnection(smp.x, smp.y);

	$this.remove();

	var path = '<svg height="5000" width="5000" data-start="class_'+cStart.id+'" data-end="class_'+cEnd.id+'">';
		path += "<path class='line' stroke-width='2px' stroke='black' d='M"+startPoint.x+" "+startPoint.y+" L"+endPoint.x+" "+endPoint.y+"'></path>";
	path += '</svg>';
	$(".umlcanvas").append(path);
}
