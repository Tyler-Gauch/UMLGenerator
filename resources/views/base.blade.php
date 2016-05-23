<!DOCTYPE html>
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

		@yield("stylesheets")
	</head>

	<body style="top:50px">
		<div class="container" style="width: 100%">
				@yield('content')
		</div>
	</body>

	<script src="/js/jquery-2.2.3.min.js"></script>
	<script src="/jquery-ui/jquery-ui.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>

	@yield("javascript")
</html>