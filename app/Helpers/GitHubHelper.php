<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Cache;

class GitHubHelper{

	const BASE_URL = "https://api.github.com";

	private $client;
	private $user;

	public function __construct($user){
		$this->client = new Client([
			'base_uri' => self::BASE_URL,
			'timeout' => 300.0
		]);
		$this->user = $user;
	}

	public function listRepos(){
		$response = $this->client->request("GET", "/user/repos?access_token={$this->user->access_token}");

		$list = json_decode((string)$response->getBody());

		return $list;
	}

	public function listBranches($repoId){
		$repos = $this->listRepos();

		foreach($repos as $key=>$repo)
		{
			if($repo->name == $repoId)
			{
				$repos = $repo;
				break;
			}
		}

		$response = $this->client->request("GET", "/repos/{$repos->owner->login}/$repoId/branches");

		$list = json_decode((string)$response->getBody());

		return $list;
	}

	public function getArchive($repoId, $branch)
	{
		$repos = $this->listRepos();

		foreach($repos as $key=>$repo)
		{
			if($repo->name == $repoId)
			{
				$repos = $repo;
				break;
			}
		}

		$response = $this->client->request("GET", "/repos/{$repos->owner->login}/$repoId/zipball/$branch");

		$filename = storage_path()."/framework/cache/{$repoId}_{$branch}_".time();

		file_put_contents($filename.".zip", $response->getBody());

		return $filename;
	}

}