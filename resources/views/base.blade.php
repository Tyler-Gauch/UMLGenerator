<html>
	<head>
		<title>UML Generator</title>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.min.css"/>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.structure.min.css"/>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.theme.min.css"/>
		<link rel="stylesheet" href="/css/bootstrap.min.css"/>
		<link rel="stylesheet" href="/css/bootstrap.min.css.map"/>
		<link rel="stylesheet" href="/css/bootstrap-theme.min.css"/>
		<link rel="stylesheet" href="/css/bootstrap-theme.min.css.map"/>
		<link rel="stylesheet" href="/css/font-awesome.min.css"/>
		<style>
			.dropdown-submenu {
			    position:relative;
			}
			.dropdown-submenu>.dropdown-menu {
			    top:0;
			    left:100%;
			    margin-top:-6px;
			    margin-left:-1px;
			    -webkit-border-radius:0 6px 6px 6px;
			    -moz-border-radius:0 6px 6px 6px;
			    border-radius:0 6px 6px 6px;
			}
			.dropdown-submenu:hover>.dropdown-menu {
			    display:block;
			}
			.dropdown-submenu>a:after {
			    display:block;
			    content:" ";
			    float:right;
			    width:0;
			    height:0;
			    border-color:transparent;
			    border-style:solid;
			    border-width:5px 0 5px 5px;
			    border-left-color:#cccccc;
			    margin-top:5px;
			    margin-right:-10px;
			}
			.dropdown-submenu:hover>a:after {
			    border-left-color:#ffffff;
			}
			.dropdown-submenu.pull-left {
			    float:none;
			}
			.dropdown-submenu.pull-left>.dropdown-menu {
			    left:-100%;
			    margin-left:10px;
			    -webkit-border-radius:6px 0 6px 6px;
			    -moz-border-radius:6px 0 6px 6px;
			    border-radius:6px 0 6px 6px;
			}

			#loader{
				position: absolute; 
				top:30%; 
				left: 30%;
				width:500px;
				height: 500px;
				text-align: center;
			}

			#loaderLabel{
				font-size: 24;
				font-weight: 150;
				text-align: center;
			}

		</style>

		@yield("stylesheets")

	</head>

	<body>
		<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/">UML Generator</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="main-nav">
      <ul class="nav navbar-nav">      
        @yield('extra_nav')
      </ul>
      <ul class="nav navbar-nav navbar-right">
      @if(Auth::check())
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/auth/logout">Logout</a></li>
          </ul>
        </li>
      @endif
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
		<div class="container" style="width: 100%; margin-top:65px">
				@yield('content')
		</div>

	<div class="modal fade" tabindex="-1" role="dialog" id="loader" data-backdrop="static" data-keyboard="false" >
	  <div class="modal-dialog">
	  	<div class="row">
	    	<div class="col-lg-12">
	    		<img src="/gears.gif"/>
	    	</div>
	    	<div class="col-lg-12" id="loaderLabel">
	    	</div>
	    </div>
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	</body>

	<script src="/js/jquery-2.2.3.min.js"></script>
	<script src="/jquery-ui/jquery-ui.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<script>
		
		window.showLoader = function(message = "Loading..."){
			$("#loader").modal("show");
			$("#loaderLabel").text(message);
		}

		window.hideLoader = function(){
			$("#loader").modal("hide");
		}		

	</script>

	@yield("javascript")
	
</html>