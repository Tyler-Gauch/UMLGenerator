<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(["prefix" => "/auth"], function(){
	Route::get("/", "Auth\\AuthController@login");
	Route::get("/github", "Auth\\AuthController@loginWithGithub");	
	Route::get('/logout', 'Auth\AuthController@getLogout');
	
});

Route::group(["middleware" => 'auth'], function(){
	Route::get('/{project?}', "DashboardController@index");

	Route::post("/parser/java", "ParserController@javaParser");
	Route::get("/parser/{repo}/{branch}", "ParserController@parseBranch");

	Route::post("/repo/list", "DashboardController@listRepos");
	Route::post("/branch/list", "DashboardController@listBranches");
	
	Route::post("/project/create", 'ProjectController@create');
	Route::get("/project/get/{project?}", 'ProjectController@get');
});
