//////////////////////////////////////////////////////
//													//
//			Edit Common Functions 					//
//													//
//////////////////////////////////////////////////////

$("#edit_delete").on("click", function(){
	var c = UMLClasses[$("#edit_target").val()];
	if(confirm("Are you sure you want to delete <strong>"+c.className+"</strong>?"))
	{
		delete UMLClasses[$("#edit_target").val()];
		$("#class_"+c.id+"_parent").remove();
		clearEditForm();
	}
});

function clearEditForm(){
	$("#edit_classname").val("");
	$("#edit_attributes").empty();
	$("#edit_attribute_add").val("");
	$("#edit_functions").empty();
	$("#edit_functions_add").val("");
	$("#edit_target").val("null");
}

//////////////////////////////////////////////////////
//													//
//			Edit ClassName Functions 				//
//													//
//////////////////////////////////////////////////////

$("#edit_classname").on("change", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.className = $("#edit_classname").val();
	c.render();
});

//////////////////////////////////////////////////////
//													//
//			Edit Attribute Functions 				//
//													//
//////////////////////////////////////////////////////

$(document).on("change", ".edit_attribute", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.attributes[$(this).data("key")] = $(this).val();
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
	$(this).parents().closest(".input-group").remove();
	c.render(c.selected);
});

$("#edit_attributes_add_btn").on("click", function(){
	if($("#edit_attributes_add").val() == "")
	{
		return;
	}
	var c = UMLClasses[$("#edit_target").val()];
	var key = c.attributes.push($("#edit_attributes_add").val());
	key--;
	$("#edit_attributes_add").val("");
	appendEditAttribute(key, c.attributes[key]);
	c.render(c.selected);
	
});

$("#edit_attributes_add").on("keyup", function(e){
	var code = e.keyCode || e.which;
	if(code == 13) { //Enter keycode
	   $("#edit_attributes_add_btn").click();
	 }
});

function appendEditAttribute(key, value){
	$("#edit_attributes").append('<div class="input-group"><input type="text" data-key="'+key+'" value="'+value+'" class="form-control edit_attribute"/><span class="input-group-btn"><button class="edit_attribute_del btn btn-danger" data-key="'+key+'">X</button></span></div>');
}

//////////////////////////////////////////////////////
//													//
//			Edit Function Functions 				//
//													//
//////////////////////////////////////////////////////

$(document).on("change", ".edit_function", function(){
	var c = UMLClasses[$("#edit_target").val()];
	c.functions[$(this).data("key")] = $(this).val();
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

	$(this).parents().closest(".input-group").remove();
	c.render(c.selected);
});

$("#edit_functions_add_btn").on("click", function(){
	if($("#edit_functions_add").val() == "")
	{
		return;
	}
	var c = UMLClasses[$("#edit_target").val()];
	var key = c.functions.push($("#edit_functions_add").val());
	key--;
	$("#edit_functions_add").val("");
	appendEditFunction(key, c.functions[key]);
	c.render(c.selected);
	
});

$("#edit_functions_add").on("keyup", function(e){
	var code = e.keyCode || e.which;
	if(code == 13) { //Enter keycode
	   $("#edit_functions_add_btn").click();
	 }
});

function appendEditFunction(key, value){
	$("#edit_functions").append('<div class="input-group"><input type="text" data-key="'+key+'" value="'+value+'" class="form-control edit_function"/><span class="input-group-btn"><button class="edit_function_del btn btn-danger" data-key="'+key+'">X</button></span></div>');
}
