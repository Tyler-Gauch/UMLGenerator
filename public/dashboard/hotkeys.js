$(document).ready(function(){
	if(!userIsGuest){
		$(document).bind('keydown', 'ctrl+s', function(e) {
		   	e.preventDefault();
			save();
		    return false;
		});

		$(".allow-hotkeys").bind("keydown", "ctrl+s", function(e){
			e.preventDefault();
			save();
		    return false;
		});
	}

	$(document).bind('keydown', 'ctrl+n', function(e) {
	    e.preventDefault();
		$(".new_project").click();
	    return false;
	});

	$(".allow-hotkeys").bind("keydown", "ctrl+n", function(e){
		e.preventDefault();
		$(".new_project").click();
	    return false;
	});
});