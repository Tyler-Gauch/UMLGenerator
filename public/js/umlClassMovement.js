var currentMatrix = null;
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
		if($("#edit_target").val() != "null")
			UMLClasses[$("#edit_target").val()].render();

		var c = UMLClasses[$(this).attr("id")];
		c.render(true, true);
		var t = $("#"+$(this).attr("id"));
		$("#edit_classname").val(c.className);
		$("#edit_target").val(t.attr("id"));

		var attrs = $("#edit_attributes");
		attrs.empty();
		$.each(c.attributes, function(key, value){appendEditAttribute(key, value)});

		var funcs = $("#edit_functions");
		funcs.empty();
		$.each(c.functions, function(key, value){appendEditFunction(key, value)});

		currentX = e.clientX;
		currentY = e.clientY;
		currentMatrix = t.attr("transform").slice(7,-1).split(" ");
		for(var i = 0; i < currentMatrix.length; i++)
		{
			currentMatrix[i] = parseFloat(currentMatrix[i]);
		}

		$("#edit_form").removeClass("hidden");
	}
});

$(document).on("mousemove touchmove", function(e){
	var element = $(".umlclass.moving");
	if(element.length > 0)
	{
		var dx = e.clientX - currentX;
		var dy = e.clientY - currentY;
		currentMatrix[4] += dx;
		currentMatrix[5] += dy;
		var c = UMLClasses[element.attr("id")];
		c.x = currentMatrix[4];
		c.y = currentMatrix[5];
		var newMatrix = "matrix("+currentMatrix.join(" ") + ")";
		element.attr('transform', newMatrix);
		currentX = e.clientX;
		currentY = e.clientY;

		$(document).find("[data-end='class_"+c.id+"']").each(function(key){
			moveRelationShip($(this));
		});
		$(document).find("[data-start='class_"+c.id+"']").each(function(key){
			moveRelationShip($(this));
		});
	}
});

$(document).on("mouseup touchend", ".umlclass", function(e){
	$(this).removeClass("moving");
	$(this).mouseenter();
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
