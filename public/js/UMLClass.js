var UMLClassID = 0;
var UMLClassX = 10;
var UMLClassY = 10;

var UMLClassSaveAll = function(successCB = function(){}, failCB = function(){}){
	
	var postData = {};
	var k  = 0;

	$.each(UMLClasses, function(key, value){
		postData[k++] = value.serialize(); 
	});

	console.log(postData);

	$.ajax({
		url: UMLClassSaveURL+"/"+$("#current_branch").val(), //declared global in dashboard.blade.php 
		method: "POST",
		data: postData,
		success: successCB,
		fail: failCB
	});
}

var UMLClass = function(config){
	var className = config.className;
	if(className == null || className == undefined || className == "")
	{
		className = "Class"+UMLClassID;
	}
	if(!$.isArray(config.attributes))
	{
		config.attributes = [];
	}

	if(!$.isArray(config.functions))
	{
		config.functions = [];
	}

	if(config.x == undefined)
	{
		config.x = UMLClassX;
	}

	if(config.y == undefined)
	{
		config.y = UMLClassY;
	}
	if(config.classType == undefined)
	{
		config.classType = "class";
	}
	this.className = className;
	this.attributes = config.attributes;
	this.functions = config.functions;
	this.relationships = config.relationships;
	this.id = UMLClassID++;
	this.top = config.top;
	this.left = config.left;
	this.x = config.x;
	this.y = config.y;
	this.classType = config.classType;
	this.selected = false;
	this.moving = false;
	this.hovering = false;
	UMLClasses["class_"+this.id] = this;
	this.render();
};

UMLClass.prototype = {
	id: -1,
	className: "Class",
	classType: "class",
	attributes: [],
	functions: [],
	relationships: [],
	x: 0,
	y: 0,
	selected: false,
	width: 200,
	text_size: 6,
	render: function(){
		var y = 0;
		var x = 0;
		var nameHeight = 45;
		var attributesHeight = this.attributes.length * 15 + 10;
		var functionsHeight = this.functions.length * 15 + 10;
		var fullheight = nameHeight+attributesHeight+functionsHeight;
		this.fullHeight = fullheight;		

		$("#class_"+this.id+"_parent").remove();

		var uml = '<svg id="class_'+this.id+'_parent">'+
			'<g class="umlclass';
			uml += '" transform="matrix(1 0 0 1 '+this.x+' '+this.y+')" id="class_'+this.id+'">'+
				'<g>';
					uml += '<rect id="rect_hover_class_'+this.id+'" class="" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+fullheight+'"></rect>';
					uml += '<rect class="umlclass-name-rect" unselectable="on" fill="#AA5439" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+nameHeight+'"></rect>'+
					'<rect class="umlclass-attributes-rect" unselectable="on" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight)+'" width="'+this.width+'" height="'+attributesHeight+'"></rect>'+
					'<rect class="umlclass-functions-rect" unselectable="on" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight+attributesHeight)+'" width="'+this.width+'" height="'+functionsHeight+'"></rect>';
			uml += '</g>'+
				'<g>'+
					'<text class="umlclass-name-text" unselectable="on" text-anchor="middle" fill="black" stroke-width="0px" x="'+(x+this.width/2)+'" y="'+(y+15)+'">&lt;&lt;'+this.classType+'&gt;&gt;</text>';
					uml += '<text class="umlclass-name-text" unselectable="on" text-anchor="middle" fill="black" stroke-width="0px" x="'+(x+this.width/2)+'" y="'+(y+30)+'">'+this.className+'</text>';
					y += nameHeight+15;
					$.each(this.attributes, (function(key, attribute){
						if(attribute.default == null)
						{
							attribute.default = "";
						}
						var text = this.getVisibilityToken(attribute.visibility)+" "+attribute.name+" : "+attribute.type;
						if(attribute.default != "")
						{
							text += " = "+attribute.default;
						}
						var extraAttributes = "";
						if(attribute.isStatic)
						{
							extraAttributes += " text-decoration='underline'";
						}
						if(attribute.isFinal)
						{
							text += " {readOnly}";
						}
						uml += '<text class="umlclass-attributes-text" fill="black" stroke-width="0px" x="'+x+'" y="'+y+'" '+extraAttributes+'>'+text+'</text>';
						y+=15;
					}).bind(this));
					y+=10;
					$.each(this.functions, (function(key, func){
						if(func.parameters.indexOf("(") != 0)
						{
							func.parameters = "("+func.parameters;
						}
						if(func.parameters.indexOf(")") != func.parameters.length -1)
						{
							func.parameters += ")";
						}
						var text = this.getVisibilityToken(func.visibility)+" "+func.name+func.parameters;
						
						if(func.type != null)
						{
							text += " : "+func.type
						}

						var extraAttributes = "";
						if(func.isStatic || func.isFinal)
						{
							extraAttributes += " text-decoration='underline'";
						}

						uml += '<text class="umlclass-functions-text" fill="black" stroke-width="0px" x="'+x+'" y="'+y+'" '+extraAttributes+'>'+text+'</text>';
						y+=15;
					}).bind(this));
				'</g>'+
			'</g>'+
		'</svg>';

		$(".umlcanvas").append(uml);

		var addedClass = $("#class_"+this.id+"_parent");

		var children = addedClass.find("text");

		var max = 0;
		$.each(children, function(key, child){
			var length = child.getComputedTextLength();
			if(length > max){
				max = length;
			}
		});
		this.width = max+10;

		var width = this.width;
		addedClass.find("rect").each(function(){
			$(this).attr("width", width);
		});
		addedClass.find("text").each(function(){
			if($(this).attr("text-anchor") == "middle" || $(this).css("text-anchor") == "middle")
			{
				$(this).attr("x", x+(width/2));
			}else{
				$(this).attr("x", x);
			}
		});
	},
	destroy: function(){
		$("#class_"+this.id+"_parent").remove();
		delete UMLClasses["class_"+this.id];
	},
	topLeft: function(){
		return {x: this.x, y: this.y};
	},
	topRight: function(){
		return {x: parseFloat(this.x)+parseFloat(this.width), y: this.y};
	},
	bottomLeft: function(){
		return {x: this.x, y: parseFloat(this.y)+parseFloat(this.fullHeight)};
	},
	bottomRight: function(){
		return {x: parseFloat(this.x)+parseFloat(this.width), y: parseFloat(this.y)+parseFloat(this.fullHeight)};
	},
	topMidPoint: function(tl, tr){
		if(tl == undefined) tl = this.topLeft();
		if(tr == undefined) tr = this.topRight();
		return window.midpoint(tl.x, tl.y, tr.x, tr.y);
	},
	leftMidPoint: function(tl, bl){
		if(tl == undefined) tl = this.topLeft();
		if(bl == undefined) bl = this.bottomLeft();
		return window.midpoint(tl.x, tl.y, bl.x, bl.y);
	},
	rightMidPoint: function(tr, br){
		if(tr == undefined) tr = this.topRight();
		if(br == undefined) br = this.bottomRight();
		return window.midpoint(tr.x, tr.y, br.x, br.y);
	},
	bottomMidPoint: function(br, bl){
		if(br == undefined) br = this.bottomRight();
		if(bl == undefined) bl = this.bottomLeft();
		return window.midpoint(bl.x, bl.y, br.x, br.y);
	},
	midPoint: function(){
		var bP = this.bottomMidPoint();
		var tP = this.topMidPoint();
		return window.midpoint(bP.x, bP.y, tP.x, tP.y);
	},
	getConnectionPoints: function(){
		var points = [];
		var tl = this.topLeft();
		var tr = this.topRight();
		var bl = this.bottomLeft();
		var br = this.bottomRight();
		// points.push(tl);
		// points.push(tr);
		// points.push(bl);
		// points.push(br);
		points.push(this.bottomMidPoint(br, bl));
		points.push(this.topMidPoint(tl, tr));
		points.push(this.leftMidPoint(tl, bl));
		points.push(this.rightMidPoint(tr, br));

		return points;
	},
	findClosestConnection: function(x,y){
		var closestPoint = null;
		var closestDistance = 0;

		var points = this.getConnectionPoints();


		for(var i = 0; i < points.length; i++)
		{
			var point = points[i];
			var distance = window.distance(x,y, point.x, point.y);
			if(closestPoint == null || distance < closestDistance)
			{
				closestPoint = point;
				closestDistance = distance;
			}
		}

		return closestPoint;
	},
	getVisibilityToken: function(visibility){
		if(visibility == "private")
		{
			return "-";
		}else if(visibility == "public")
		{
			return "+";
		}else if(visibility == "protected")
		{
			return "#";
		}
	},
	addRelationship: function(className, type){
		var umlClass = null;
		$.each(UMLClasses, function(key, value){
			if(value.className == className)
			{
				umlClass = value;
			}
		});
		if(umlClass == null)
		{
			this.relationships = $.grep(this.relationships, function(value){
				return value != className;
			});
			return;
		}

		console.log("Adding relationship from " + this.className + " to " + className + " of type " + type);

		var emp = this.midPoint();
		var smp = umlClass.midPoint();
		var startPoint = this.findClosestConnection(smp.x, smp.y);
		var endPoint = umlClass.findClosestConnection(emp.x, emp.y);

		var exists = false;

		var $path = null

		$(document).find("[data-end='class_"+this.id+"'][data-start='class_"+umlClass.id+"']").each(function(){
			exists = true;
			$path = $(this);
		});
		$(document).find("[data-start='class_"+this.id+"'][data-end='class_"+umlClass.id+"']").each(function(){
			exists = true;
			$path = $(this);
		});

		if(exists)
		{
			var startMarker = $($path.children()[0]).attr("marker-start");
			var endMarker = $($path.children()[0]).attr("marker-end");
			var dotted = $($path.children()[0]).attr("stroke-dasharray");
			//check if we have the propper marker for this class
			if(type == "implements" && (startMarker != "url(#arrowEmpty)" || dotted != "5,5"))
			{
				$path.attr("marker-start","url(#arrowEmpty)");
				$path.attr("stoke-dasharray", "5,5");
			}else if(type == "inherits" && startMarker != "url(#arrowEmpty)")
			{
				$path.attr("marker-start", "url(#arrowEmpty)");
			}else if(type == "aggregation" && startMarker != "url(#diamondEmpty)")
			{
				$path.attr("marker-start", "url(#diamondEmpty)");
			}else if(type == "composite-aggregation" && startMarker != "url(#diamondFill)")
			{
				$path.attr("marker-start", "url(#diamondFill)");
			}
			return;
		}

		var classes = "line";
		if(this.hovering || umlClass.hovering)
		{
			classes += " hover";
		}

		var path = '<svg height="5000" width="5000" data-start="class_'+this.id+'" data-end="class_'+umlClass.id+'">';
			path += "<path id='relationship_class_"+this.id+"_class_"+umlClass.id+"' class='"+classes+"' stroke-width='2px' stroke='black' d='M"+startPoint.x+" "+startPoint.y+" L"+endPoint.x+" "+endPoint.y+"'";

			if(type == "implements")
			{
				path += " marker-end='url(#arrowEmpty)' stroke-dasharray='5,5'";
			}else if(type == "inherits")
			{
				path += " marker-end='url(#arrowEmpty)'";
			}else if(type == "aggregation")
			{
				path += " marker-end='url(#diamondEmpty)'";
			}else if(type == "composite-aggregation")
			{
				path += " marker-end='url(#diamondFill)'";
			}

			path += "></path>";
		path += '</svg>';

		$(".umlcanvas").append(path);
		$("#relationship_class_"+this.id+"_class_"+umlClass.id).click();
	},
	select: function(){
		this.selected = true;
		$("#rect_hover_class_"+this.id).addClass("selected");
		return this;
	},
	unselect: function(){
		this.selected = false;
		$("#rect_hover_class_"+this.id).removeClass("selected");
		return this;
	},
	hover: function(){
		this.hovering = true;
		$("#rect_hover_class_"+this.id).addClass("hover");

		$("[data-end='class_"+this.id+"']").each(function(value){
			$(this).children().addClass("hover");
		});
		$("[data-start='class_"+this.id+"']").each(function(value){
			$(this).children().addClass("hover");
		});

		return this;
	},
	unhover: function(){
		this.hovering = false;
		$("#rect_hover_class_"+this.id).removeClass("hover");

		$("[data-end='class_"+this.id+"']").each(function(value){
			$(this).children().removeClass("hover");
		});
		$("[data-start='class_"+this.id+"']").each(function(value){
			$(this).children().removeClass("hover");
		});

		return this;
	},
	getNode: function(){
		return $("#class_"+this.id);
	},
	serialize: function(){

		var funcs = [];
		$.each(this.functions, function(key, value){
			var f  = {};
			f["name"] = (value.name == undefined ? "NO_NAME" : value.name);
			f["visibility"] = (value.visibility == undefined ? "public" : value.visibility);
			f["type"] = (value.type == undefined ? "" : value.type);
			f["parameters"] = (value.parameters == undefined ? "()" : value.parameters);
			f["isStatic"] = (value.isStatic == undefined ? false : value.isStatic);
			f["isFinal"] = (value.isFinal == undefined ? false : value.isFinal);
			f["isAbstract"] = (value.isAbstract == undefined ? false : value.isAbstract);
			if(f.length < 7)
			{
				console.log(f);
			}
			funcs.push(f);
		});

		var attr = [];
		$.each(this.attributes, function(key, value){
			var a  = {};
			a["name"] = (value.name == undefined ? "NO_NAME" : value.name);
			a["visibility"] = (value.visibility == undefined ? "public" : value.visibility);
			a["type"] = (value.type == undefined ? "" : value.type);
			a["default"] = (value.default == undefined ? "()" : value.default);
			a["isStatic"] = (value.isStatic == undefined ? false : value.isStatic);
			a["isFinal"] = (value.isFinal == undefined ? false : value.isFinal);
			a["isAbstract"] = (value.isAbstract == undefined ? false : value.isAbstract);

			attr.push(a);
		});

		return {
			x: this.x,
			y: this.y,
			className: this.className,
			type: this.classType,
			attributes: attr,
			functions: funcs,
			relationships: this.relationships
		};
	},
	save: function(successCB = function(){}, failCB = function(){}){
		$.ajax({
			url: UMLClassSaveURL,
			method: method,
			data: this.serialize(),
			success: successCB,
			fail: failCB
		});
	}
}