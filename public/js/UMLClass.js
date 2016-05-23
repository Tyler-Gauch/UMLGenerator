var UMLClassID = 0;
var UMLClass = function(config){
	var className = config.classname;
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
		config.x = 100;
	}

	if(config.y == undefined)
	{
		config.y = 100;
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
	UMLClasses["class_"+this.id] = this;
	this.render();
};

UMLClass.prototype = {
	id: -1,
	className: "Class",
	attributes: [],
	functions: [],
	relationships: [],
	x: 0,
	y: 0,
	selected: false,
	width: 200,
	render: function(selected = false, moving = false){
		this.selected = selected;
		var y = 0;
		var x = 0;
		var nameHeight = 30;
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
			uml += '" transform="matrix(1 0 0 1 '+this.x+' '+this.y+')" id="class_'+this.id+'">'+
				'<g>';
					if(selected){
						uml += '<rect class="selected" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+fullheight+'"></rect>';
					}
					uml += '<rect class="umlclass-name-rect" fill="#AA5439" x="'+x+'" y="'+y+'" width="'+this.width+'" height="'+nameHeight+'"></rect>'+
					'<rect class="umlclass-attributes-rect" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight)+'" width="'+this.width+'" height="'+attributesHeight+'"></rect>'+
					'<rect class="umlclass-functions-rect" fill="#FF7144" stroke-width="1px" stroke="#000" x="'+x+'" y="'+(y+nameHeight+attributesHeight)+'" width="'+this.width+'" height="'+functionsHeight+'"></rect>';
			uml += '</g>'+
				'<g>'+
					'<text class="umlclass-name-text" text-anchor="middle" fill="black" stroke-width="0px" x="'+(x+100)+'" y="'+(y+15)+'">'+this.className+'</text>';
					y += nameHeight+15;
					$.each(this.attributes, function(key, attribute){
						uml += '<text class="umlclass-attributes-text" fill="black" stroke-width="0px" x="'+(x+5)+'" y="'+y+'">'+attribute+'</text>';
						y+=15;
					})
					y+=10;
					$.each(this.functions, function(key, func){
						uml += '<text class="umlclass-functions-text" fill="black" stroke-width="0px" x="'+(x+5)+'" y="'+y+'">'+func+'</text>';
						y+=15;
					})
				'</g>'+
			'</g>'+
		'</svg>';

		$(".umlcanvas").append(uml);
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
	topMidPoint: function(){
		var tl = this.topLeft();
		var tr = this.topRight();
		return midpoint(tl.x, tl.y, tr.x, tr.y);
	},
	leftMidPoint: function(){
		var tl = this.topLeft();
		var bl = this.bottomLeft();
		return midpoint(tl.x, tl.y, bl.x, bl.y);
	},
	rightMidPoint: function(){
		var tr = this.topRight();
		var br = this.bottomRight();
		return midpoint(tr.x, tr.y, br.x, br.y);
	},
	bottomMidPoint: function(){
		var br = this.bottomRight();
		var bl = this.bottomLeft();
		return midpoint(bl.x, bl.y, br.x, br.y);
	},
	midPoint: function(){
		var bP = this.bottomMidPoint();
		var tP = this.topMidPoint();
		return midpoint(bP.x, bP.y, tP.x, tP.y);
	}

}