@extends("base")

@section("stylesheets")
	<link rel="stylesheet" href="/bootstrap-social-gh-pages/bootstrap-social.css"/>
	<link rel="stylesheet" href="/bootstrap-social-gh-pages/bootstrap-social.less"/>
	<link rel="stylesheet" href="/bootstrap-social-gh-pages/bootstrap-social.scss"/>
@endsection

@section("content")
	<div class="container">
		<div class="row">
			<div class="col-lg-6">
				<legend>Bio Here</legend>
			</div>
			<div class="col-lg-6">
				<legend>Sign in with...</legend>
				<div class="col-lg-12">
					@if(isset($github))
						<a class="btn btn-social btn-block btn-github" href="{{ $github }}"><span class="fa fa-github"></span> GitHub</a>
					@endif
					<br/>
					<a href="/">Continue as guest</a>
				</div>
			</div>
		</div>
	</div>
@endsection

@section("javascript")
@endsection