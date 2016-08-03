//////////////////////////////////////////////////////
//													//
//			Edit Common Functions 					//
//													//
//////////////////////////////////////////////////////

$(document).on("click", ".edit_expand", function(){
	var target = $("#"+$(this).data("target"));
	var text = $(this).children("span");
	if (target.css("display") == "none"){
		text.removeClass("fa-arrow-circle-down");
		text.addClass("fa-arrow-circle-up");
	}else{
		text.addClass("fa-arrow-circle-down");
		text.removeClass("fa-arrow-circle-up");
	}

	target.slideToggle();
});

$("#edit_delete").on("click", function(){
	deleteClass();
});

$(document).on("keyup", function(e){
	var code = e.keyCode || e.which;
	if(code == 46) { //delete keycode
		e.preventDefault();
	   deleteClass();
	}
});

function clearEditForm(){
	$("#edit_classname").val("");
	$("#edit_attributes").empty();
	$("#edit_attribute_add").val("");
	$("#edit_functions").empty();
	$("#edit_functions_add").val("");
	$("#edit_target").val("null");
	$("#edit_form").addClass("hidden");
	$("#list_view").removeClass("hidden");
	$("#edit_line_form").addClass("hidden");

}

function deleteClass(){
	var c = UMLClasses[$("#edit_target").val()];
	if(confirm("Are you sure you want to delete "+c.className+"?"))
	{
		$(document).find("[data-end='class_"+c.id+"']").each(function(key){
			$(this).remove();
		});
		$(document).find("[data-start='class_"+c.id+"']").each(function(key){
			$(this).remove();
		});
		delete UMLClasses[$("#edit_target").val()];
		$("#class_"+c.id+"_parent").remove();
		removeClassFromListView(c.id);
		clearEditForm();
	}
}

$(".edit_back").on("click", function(){
	clearEditForm();
	$("#edit_line_form").addClass("hidden");

});

//////////////////////////////////////////////////////
//													//
//			Edit ClassName Functions 				//
//													//
//////////////////////////////////////////////////////

$("#edit_classname").on("change", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.className = $("#edit_classname").val();
	c.render(c.selected);
});

$("#edit_class_type").on("change", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.classType = $(this).val();
	c.render(c.selected);
});

//////////////////////////////////////////////////////
//													//
//			Edit Attribute Functions 				//
//													//
//////////////////////////////////////////////////////

$(document).on("change", ".edit_attribute", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.attributes[$(this).data("key")].name = $(this).val();
	c.render(c.selected);
});

$(document).on("click", ".edit_attribute_del", function(){
	var c = UMLClasses[$("#edit_target").val()];
	var key = $(this).data("key");
	c.attributes.splice(key,1);
	$(".edit_attribute_del").each(function(k){
		if($(this).data("key") > key)
		{
			$(this).data("key", $(this).data("key")-1);
		}
	});
	$(".edit_attribute").each(function(k){
		if($(this).data("key") > key)
		{
			$(this).data("key", $(this).data("key")-1);
		}
	});
	$("#"+$(this).data("target")).remove();
	c.render(c.selected);
});

$("#edit_attributes_add_btn").on("click", function(){
	if($("#edit_attributes_add").val() == "")
	{
		return;
	}
	var c = UMLClasses[$("#edit_target").val()];
	var attr = {name: $("#edit_attributes_add").val(), visibility: "public", type:"void", default: null, isStatic: false, isFinal: false, isAbstract: false};
	var key = c.attributes.push(attr);
	key--;
	$("#edit_attributes_add").val("");
	appendEditAttribute(key, c);
	c.render(c.selected);
});

$("#edit_attributes_add").on("keyup", function(e){
	var code = e.keyCode || e.which;
	if(code == 13) { //Enter keycode
	   $("#edit_attributes_add_btn").click();
	 }
});

$(document).on("change", ".edit_attributes_value", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.attributes[$(this).data("key")];

	if(val == "" || val == null)
	{
		attr.default = "";
	}else{
		attr.default = val;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_attributes_type", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.attributes[$(this).data("key")];

	if(val == "" || val == null)
	{
		attr.type = "void";
	}else{
		attr.type = val;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_attributes_static", function(){
	console.log("Static changed");
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.attributes[$(this).data("key")];

	if(this.checked)
	{
		attr.isStatic = true;
	}else{
		attr.isStatic = false;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_attributes_final", function(){
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.attributes[$(this).data("key")];

	if(this.checked)
	{
		attr.isFinal = true;
	}else{
		attr.isFinal = false;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_attributes_vis", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.attributes[$(this).data("key")];
	attr.visibility = val;
	c.render(c.selected);
});

function appendEditAttribute(key, umlClass){
	var id = umlClass.id+'_'+key;
	var html = '<div class="col-lg-12" id="edit_attributes_'+id+'">'+
					'<div class="input-group">'+
						'<input type="text" class="edit_attributes form-control" data-key="'+key+'" value="'+umlClass.attributes[key].name+'"/>'+
						'<span class="input-group-btn">'+
							'<button class="btn btn-success edit_expand" data-target="edit_attributes_expand_content_'+id+'"><span class="fa fa-arrow-circle-down"></span></button>'+
						'</span>'+
					'</div>'+
					'<div class="col-lg-12" id="edit_attributes_expand_content_'+id+'" style="background-color: #ccc; display:none">'+
						'<br/>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Visibility: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<select class="form-control edit_attributes_vis" data-key="'+key+'">'+
									'<option value="public"';
									if(umlClass.attributes[key].visibility == "public")
									{
										html += " selected ";
									}
									html += '>Public</option>'+
									'<option value="private"';
									if(umlClass.attributes[key].visibility == "private")
									{
										html += " selected ";
									}
									html += '>Private</option>'+
									'<option value="protected"';
									if(umlClass.attributes[key].visibility == "protected")
									{
										html += " selected ";
									}
									html += '>Protected</option>'+
								'</select>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Type: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="text" class="form-control edit_attributes_type" data-key="'+key+'" value="'+umlClass.attributes[key].type+'"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Value: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="text" class="form-control edit_attributes_value" data-key="'+key+'" value="'+umlClass.attributes[key].default+'"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Static: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="checkbox" class="edit_attributes_static" data-key="'+key+'"';
								if(umlClass.attributes[key].isStatic)
								{
									html += "checked";
								}
								html += '"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Constant: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="checkbox" class="edit_attributes_final" data-key="'+key+'"';
								if(umlClass.attributes[key].isFinal)
								{
									html += "checked";
								}
								html += '"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<button class="edit_attribute_del btn btn-danger" data-key="'+key+'" data-target="edit_attributes_'+id+'">Delete</button>'+
						'</div>'+
						'<br/>'+
					'</div>'+
				'</div>';
	$("#edit_attributes").append(html);
}

//////////////////////////////////////////////////////
//													//
//			Edit Function Functions 				//
//													//
//////////////////////////////////////////////////////

$(document).on("change", ".edit_function", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.functions[$(this).data("key")].name = $(this).val();
	c.render(c.selected);
});

$(document).on("click", ".edit_function_del", function(){
	var c = UMLClasses[$("#edit_target").val()];
	var key = $(this).data("key");
	c.functions.splice(key,1);
	$(".edit_function_del").each(function(k){
		if($(this).data("key") > key)
		{
			$(this).data("key", $(this).data("key")-1);
		}
	});
	$(".edit_function").each(function(k){
		if($(this).data("key") > key)
		{
			$(this).data("key", $(this).data("key")-1);
		}
	});

	$("#"+$(this).data("target")).remove();
	c.render(c.selected);
});

$("#edit_functions_add_btn").on("click", function(){
	if($("#edit_functions_add").val() == "")
	{
		return;
	}
	var c = UMLClasses[$("#edit_target").val()];
	var func = {name: $("#edit_functions_add").val(), visibility: "public", type: "void", parameters: "", isStatic: false, isFinal: false, isAbstract: false};
	var key = c.functions.push(func);
	key--;
	$("#edit_functions_add").val("");
	appendEditFunction(key, c);
	c.render(c.selected);
	
});

$("#edit_functions_add").on("keyup", function(e){
	var code = e.keyCode || e.which;
	if(code == 13) { //Enter keycode
	   $("#edit_functions_add_btn").click();
	 }
});

$(document).on("change", ".edit_functions_type", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.functions[$(this).data("key")];

	if(val == "" || val == null)
	{
		attr.type = "void";
	}else{
		attr.type = val;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_functions_static", function(){
	console.log("Static changed");
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.functions[$(this).data("key")];

	if(this.checked)
	{
		attr.isStatic = true;
	}else{
		attr.isStatic = false;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_functions_final", function(){
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.functions[$(this).data("key")];

	if(this.checked)
	{
		attr.isFinal = true;
	}else{
		attr.isFinal = false;
	}

	c.render(c.selected);
});

$(document).on("change", ".edit_functions_vis", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.functions[$(this).data("key")];
	attr.visibility = val;
	c.render(c.selected);
});

$(document).on("change", ".edit_functions_params", function(){
	var val = $(this).val();
	var c = UMLClasses[$("#edit_target").val()];
	var attr = c.functions[$(this).data("key")];
	attr.parameters = val;
	c.render(c.selected);
});

function appendEditFunction(key, umlClass){
	var id = umlClass.name+"_"+key;
	var html = '<div class="col-lg-12" id="edit_functions_'+id+'">'+
					'<div class="input-group">'+
						'<input type="text" class="edit_functions form-control" data-key="'+key+'" value="'+umlClass.functions[key].name+'"/>'+
						'<span class="input-group-btn">'+
							'<button class="btn btn-success edit_expand" data-target="edit_functions_expand_content_'+id+'"><span class="fa fa-arrow-circle-down"></span></button>'+
						'</span>'+
					'</div>'+
					'<div class="col-lg-12" id="edit_functions_expand_content_'+id+'" style="background-color: #ccc; display:none">'+
						'<br/>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Visibility: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<select class="form-control edit_functions_vis" data-key="'+key+'">'+
									'<option value="public"';
									if(umlClass.functions[key].visibility == "public")
									{
										html += " selected ";
									}
									html += '>Public</option>'+
									'<option value="private"';
									if(umlClass.functions[key].visibility == "private")
									{
										html += " selected ";
									}
									html += '>Private</option>'+
									'<option value="protected"';
									if(umlClass.functions[key].visibility == "protected")
									{
										html += " selected ";
									}
									html += '>Protected</option>'+
								'</select>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Type: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="text" class="form-control edit_functions_type" data-key="'+key+'" value="'+umlClass.functions[key].type+'"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Params: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="text" class="form-control edit_functions_params" data-key="'+key+'" value="'+umlClass.functions[key].parameters+'"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Static: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="checkbox" class="edit_functions_static" data-key="'+key+'"';
								if(umlClass.functions[key].isStatic)
								{
									html += "checked";
								}
								html += '"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-4">'+
								'<label>Constant: </label>'+
							'</div>'+
							'<div class="col-lg-8">'+
								'<input type="checkbox" class="edit_functions_final" data-key="'+key+'"';
								if(umlClass.functions[key].isFinal)
								{
									html += "checked";
								}
								html += '"/>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<button class="edit_function_del btn btn-danger" data-key="'+key+'" data-target="edit_functions_'+id+'">Delete</button>'+
						'</div>'+
						'<br/>'+
					'</div>'+
				'</div>';

	$("#edit_functions").append(html);
}
