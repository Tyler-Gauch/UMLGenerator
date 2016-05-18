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

	if(config.top == undefined)
	{
		config.top = $("#parent").height()/2;
	}

	if(config.left == undefined)
	{
		config.left = $("#parent").width()/2;
	}
	this.className = className;
	this.attributes = config.attributes;
	this.functions = config.functions;
	this.relationships = config.relationships;
	this.id = UMLClassID++;
	this.top = config.top;
	this.left = config.left;
	this.render();
};

UMLClass.prototype = {
	id: -1,
	className: "Class",
	attributes: [],
	functions: [],
	relationships: [],
	parent: null,
	top: null,
	left: null,
	render: function(){

		var parent = $("#parent");


		var html = "<div class='umlclass' id='umlclass-"+this.id+"' style='top: "+this.top+"px !important; left:"+this.left+"px !important; position: absolute; z-index: 1000'>";

			html += "<div class='umlclass-name'>";
				html += this.className;
			html += "</div>";

			var striped = true;

			html += "<div class='umlclass-attributes'>";
				html += "<ul>";
					$.each(this.attributes, function(key, attribute){
						if(!striped)
							html += "<li>"+attribute+"</li>";
						else
							html += "<li class='striped'>"+attribute+"</li>";

						striped = !striped;
					});
				html += "</ul>"
			html += "</div>";

			html += "<div class='umlclass-functions'>";
				html += "<ul>";
					$.each(this.functions, function(key, func){
						if(!striped)
							html += "<li>"+func+"</li>";
						else
							html += "<li class='striped'>"+func+"</li>";

						striped = !striped;
					});
				html += "</ul>"
			html += "</div>";

		html += "</div>";

		parent.append(html);
		this.bind();
	},
	bind: function(){
		var umlThis = this;
		var umlObject = $("#umlclass-"+umlThis.id);
		umlObject.unbind();
		umlObject.css("position", "relative");
		var title = umlObject.find(".umlclass-name");

		title.on("dblclick", function(){
			if(title.data("edit") == 1)
			{
				title.data("edit", 0);
				var newClassName = $("#umlclass-"+umlThis.id+"_title_edit").val();
				if(newClassName != "")
				{
					umlThis.className = newClassName;
					title.html(newClassName);
				}else{
					title.html(umlThis.className);
				}
			}else{
				var textbox = "<input type='text' id='umlclass-"+umlThis.id+"_title_edit' value='"+umlThis.className+"'/>";
				title.html(textbox);
				var $textbox = $("#umlclass-"+umlThis.id+"_title_edit");
				$textbox.focus();
				$textbox.val($textbox.val());
				$textbox.on("change blur", function(){
					title.data("edit", 1);
					title.dblclick();
				});
			}
		});

		umlObject.draggable({
			containment: ".parent-container",
			start: function(){
				umlObject.css("z-index", 1000);
			},
			stop: function(){
				umlObject.css("z-index", 500);
			}
		});

	},
	title: function(){
		return $("#umlclass-"+this.id).find(".umlclass-name");
	}

}