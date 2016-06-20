var UMLClassID = 0;
var UMLClassX = 10;
var UMLClassY = 10;
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

	if(!$.isArray(config.relationships))
	{
		config.relationships = [];
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
	this.hover = false;
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
	render: function(selected = false, moving = false, hover = false){
		this.selected = selected;
		this.moving = moving;
		this.hover = hover;
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
			if(moving){
				uml += " moving";
			}
			if(selected){
				uml += " massmove";
			}
			uml += '" transform="matrix(1 0 0 1 '+this.x+' '+this.y+')" id="class_'+this.id+'">'+
				'<g>';
					if(selected){
						uml += '<rect class="selected" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+fullheight+'"></rect>';
					}
					if(hover){
						uml += '<rect class="hover" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+fullheight+'"></rect>';
					}
					uml += '<rect class="umlclass-name-rect" unselectable="on" fill="#AA5439" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+nameHeight+'"></rect>'+
					'<rect class="umlclass-attributes-rect" unselectable="on" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight)+'" width="'+this.width+'" height="'+attributesHeight+'"></rect>'+
					'<rect class="umlclass-functions-rect" unselectable="on" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight+attributesHeight)+'" width="'+this.width+'" height="'+functionsHeight+'"></rect>';
			uml += '</g>'+
				'<g>'+
					'<text class="umlclass-name-text" unselectable="on" text-anchor="middle" fill="black" stroke-width="0px" x="'+(x+this.width/2)+'" y="'+(y+15)+'">&lt;&lt;'+this.classType+'&gt;&gt;</text>';
					uml += '<text class="umlclass-name-text" unselectable="on" text-anchor="middle" fill="black" stroke-width="0px" x="'+(x+this.width/2)+'" y="'+(y+30)+'">'+this.className+'</text>';
					y += nameHeight+15;
					$.each(this.attributes, (function(key, attribute){
						var text = this.getVisibilityToken(attribute.visibility)+" "+attribute.name+" : "+attribute.type;
						if(attribute.default != null)
						{
							text += " = "+attribute.default;
						}
						var extraAttributes = "";
						if(attribute.isStatic || attribute.isFinal)
						{
							extraAttributes += " text-decoration='underline'";
						}
						uml += '<text class="umlclass-attributes-text" fill="black" stroke-width="0px" x="'+x+'" y="'+y+'" '+extraAttributes+'>'+text+'</text>';
						y+=15;
					}).bind(this));
					y+=10;
					$.each(this.functions, (function(key, func){
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

		$("[data-end='class_"+this.id+"']").each(function(value){
			if(hover)
				$(this).children().addClass("hover");
			else
				$(this).children().removeClass("hover");
		});
		$("[data-start='class_"+this.id+"']").each(function(value){
			if(hover)
				$(this).children().addClass("hover");
			else
				$(this).children().removeClass("hover");
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
		return {x: this.x+this.width, y: this.y};
	},
	bottomLeft: function(){
		return {x: this.x, y: this.y+this.fullHeight};
	},
	bottomRight: function(){
		return {x: this.x+this.width, y: this.y+this.fullHeight};
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
	addRelationship: function(className){
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

		var emp = this.midPoint();
		var smp = umlClass.midPoint();
		var startPoint = this.findClosestConnection(emp.x, emp.y);
		var endPoint = umlClass.findClosestConnection(smp.x, smp.y);

		$(document).find("[data-end='class_"+this.id+"'][data-start='class_"+umlClass.id+"']").each(function(){
			$(this).remove();
		});
		$(document).find("[data-start='class_"+this.id+"'][data-end='class_"+umlClass.id+"']").each(function(){
			$(this).remove();
		});

		var classes = "line";
		if(this.hover || ulmClass.hover)
		{
			classes += " hover";
		}

		var path = '<svg height="5000" width="5000" data-start="class_'+this.id+'" data-end="class_'+umlClass.id+'">';
			path += "<path class='"+classes+"' stroke-width='2px' stroke='black' d='M"+startPoint.x+" "+startPoint.y+" L"+endPoint.x+" "+endPoint.y+"'></path>";
		path += '</svg>';
		$(".umlcanvas").append(path);
	}
}