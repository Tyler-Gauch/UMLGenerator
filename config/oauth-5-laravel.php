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
			'client_id'     => '505864e9645075bbb2c8',
			'client_secret' => 'eb422493d16de15aeb797ab87243198d1f8d4bd6',
			'scope'         => ['user', 'read:repo_hook'],
		],

	]

];