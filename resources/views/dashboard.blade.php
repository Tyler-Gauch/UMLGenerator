@extends("base")

@section("stylesheets")
	<style>
		.parent-container{
			width: 100%;
			height: 75%;
			border: 1px solid;
			overflow: scroll;
		}
		.umlcanvas{
			width: 5000px;
			height: 5000px;
		}
		.umlclass{
			border: 5px solid;
			display: inline-block;
			border-radius: 10px;
			fill: #CCCCCC;
			stroke-width: 1px;
			stroke: #000;
		}

		.umlclass-attributes-rect{
			fill: #827C7C;
			stroke-width: 1px;
			stroke: #000;
		}
		.umlclass-attributes-text{
			fill: black;
			stroke-width: 0px;
		}

		.umlclass-functions-rect{
			fill: #CCCCCC;
			stroke-width: 1px;
			stroke: #000;
		}
		.umlclass-functions-text{
			fill: black;
			stroke-width: 0px;
		}

		.umlclass-name-rect{
			fill: #CCCCCC;
		}
		.umlclass-name-text{
			text-anchor: middle;
			fill: black;
			stroke-width: 0px;
		}
		



	</style>
@endsection

@section("content")

	<div class="parent-container" id="parent">
		<svg class="umlcanvas">

		</svg>
	</div>
	<div class="">
		<button id="add_class">Add</button>
		<button id="refresh">Refresh</button>
	</div>


@endsection

@section("javascript")
	<script src="/js/UMLClass.js"></script>
	<script>
		
		$(document).ready(function(){
			var i = 0;
			$("#add_class").on("click", function(){
				var umlclass = new UMLClass({className:"Class "+(i++), attributes:["a", "b", "c", "d",'e','f',"g", "a", "b", "c", "d",'e','f',"g"], functions:['c', 'd', "g"]});
			});

			var currentMatrix = null;
			var currentX = null;
			var currentY = null;

			$(document).on("mousedown", ".umlclass", function(e)
			{
				if(e.which == 1)
				{
					$(".delete").remove();
					$(".umlclass").removeClass("selected");
					$(this).addClass("selected");
					currentX = e.clientX;
					currentY = e.clientY;
					currentMatrix = $(this).attr("transform").slice(7,-1).split(" ");
					for(var i = 0; i < currentMatrix.length; i++)
					{
						currentMatrix[i] = parseFloat(currentMatrix[i]);
					}
				}
			});

			$(document).on("mousemove", function(e){
				var element = $(".umlclass.selected");
				if(element.length > 0)
				{
					var dx = e.clientX - currentX;
					var dy = e.clientY - currentY;
					currentMatrix[4] += dx;
					currentMatrix[5] += dy;
					UMLClasses[element.attr("id")].x = currentMatrix[4];
					UMLClasses[element.attr("id")].y = currentMatrix[5];
					var newMatrix = "matrix("+currentMatrix.join(" ") + ")";
					element.attr('transform', newMatrix);
					currentX = e.clientX;
					currentY = e.clientY;
				}
			});

			$(document).on("mouseup", ".umlclass", function(e){
				$(this).removeClass("selected");
				$(this).mouseenter();
			});

			$("#refresh").on("click", function(){
				$.each(UMLClasses, function(key, c){
					c.render();
				});
			});

			$(document).on("mouseenter", ".umlclass", function(e){
				var c = UMLClasses[$(this).attr("id")];
				var dButton = "<button data-target='"+$(this).attr("id")+"' class='btn delete' style='top:"+(c.y+10)+"; left:"+(c.x+10)+"; position:absolute'>X</button>";
				$("body").append(dButton);
			});
			
			$(document).on("mouseleave", ".umlclass", function(event){
				var e = event.toElement || event.relatedTarget;
				if (e.tagName.toLowerCase() == "button") {
			        return;
			    }
				$(".delete").remove();
			});

			$(document).on("click", ".delete", function(){
				UMLClasses[$(this).data("target")].destroy();
				$(".delete").remove();
			});

		});

	</script>
@endsection