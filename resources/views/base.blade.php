<html>
	<head>
		<title>UML Generator</title>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.min.css"/>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.structure.min.css"/>
		<link rel="stylesheet" href="/jquery-ui/jquery-ui.theme.min.css"/>

		@yield("stylesheets")
	</head>

	<body>
		@yield('content')
	</body>

	<script src="/js/jquery-2.2.3.min.js"></script>
	<script src="/jquery-ui/jquery-ui.min.js"></script>

	@yield("javascript")
</html>