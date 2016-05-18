@extends("base")

@section("stylesheets")
	<style>
		.parent-container{
			height: 75%;
			border: 1px solid;
			overflow: scroll;
		}
		.umlclass{
			border: 1px solid;
			display: inline-block;
			border-radius: 10px;
			background-color: #CCCCCC;
		}
		.umlclass li{
			list-style: none;
			padding-left: 10px;
			padding-right: 10px;
			border-bottom: 1px solid;
		}
		.umlclass ul{
			padding: 0px !important;
			margin: 0px !important;
		}
		.umlclass li.striped{
			background-color: #827C7C;
		}
		.umlclass-attributes{
		}
		.umlclass-functions{
		}
		.umlclass-functions ul li:last-child{
			border: none !important;
		}
		.umlclass-name{
			text-align:center;
			font-size: 24px;
			border-bottom: 1px solid;
		}



	</style>
@endsection

@section("content")

	<div class="parent-container" id="parent">
	</div>
	<div class="">
		<button id="add_class">Add</button>
	</div>


@endsection

@section("javascript")
	<script src="/js/UMLClass.js"></script>
	<script>
		
		$(document).ready(function(){
				
			$("#add_class").on("click", function(){
				var umlClass = new UMLClass({
					
				});
				umlClass.title().dblclick();
			});
		});

	</script>
@endsection