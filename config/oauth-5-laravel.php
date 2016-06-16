<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => '\\OAuth\\Common\\Storage\\Session',

	/**
	 * Consumers
	 */
	'consumers' => [

		'GitHub' => [
			'client_id'     => env("GITHUB_CLIENT_ID"),
			'client_secret' => env("GITHUB_CLIENT_SECRET"),
			'scope'         => ['user', 'read:repo_hook'],
		],

	]

];