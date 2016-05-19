var UMLClassID = 0;
var UMLClasses = {};

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
	render: function(){
		var y = 0;
		var x = 0;
		var nameHeight = 30;
		var attributesHeight = this.attributes.length * 15 + 10;
		var functionsHeight = this.functions.length * 15 + 10;
		
		$("#class_"+this.id+"_parent").remove();

		var uml = '<svg id="class_'+this.id+'_parent">'+
			'<g class="umlclass" transform="matrix(1 0 0 1 '+this.x+' '+this.y+')" id="class_'+this.id+'">'+
				'<g>'+
					'<rect class="umlclass-name-rect" x="'+x+'" y="'+y+'" width="200" height="'+nameHeight+'" ></rect>'+
					'<rect class="umlclass-attributes-rect" x="'+x+'" y="'+(y+nameHeight)+'" width="200" height="'+attributesHeight+'"/></rect>'+
					'<rect class="umlclass-functions-rect" x="'+x+'" y="'+(y+nameHeight+attributesHeight)+'" width="200" height="'+functionsHeight+'"/></rect>'+
				'</g>'+
				'<g>'+
					'<text class="umlclass-name-text" x="'+(x+100)+'" y="'+(y+15)+'">'+this.className+'</text>';
					y += nameHeight+15;
					$.each(this.attributes, function(key, attribute){
						uml += '<text class="umlclass-attributes-text" x="'+(x+5)+'" y="'+y+'">'+attribute+'</text>';
						y+=15;
					})
					y+=10;
					$.each(this.functions, function(key, func){
						uml += '<text class="umlclass-functions-text" x="'+(x+5)+'" y="'+y+'">'+func+'</text>';
						y+=15;
					})
				'</g>'+
			'</g>'+
		'</svg>';

		$(".umlcanvas").append(uml);
	},
	destroy: function(){
	}
}