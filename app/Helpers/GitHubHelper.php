<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Cache;
use App\Models\Project;

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

		if($response->getStatusCode() != '200')
		{
			return [];
		}

		$list = json_decode((string)$response->getBody());

		return $list;
	}

	public function listBranches($repoId, $asArray = false){
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

		if($response->getStatusCode() != '200')
		{
			return [];
		}

		$list = json_decode((string)$response->getBody(), $asArray);

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

		if($response->getStatusCode() != '200')
		{
			return null;
		}

		$filename = storage_path()."/framework/cache/{$repoId}_{$branch}_".time();

		file_put_contents($filename.".zip", $response->getBody());

		return $filename;
	}

	//will check input url and see if url is correct and then download the url if 
	//wanted
	public static function downloadPublicRepo($url, $branch, $download = true)
	{
		//repo must be in the format https://github.com/USERNAME/REPO
		if(!\preg_match('/^(https:\/\/github.com\/[^\/]+\/[^\/]+)$/', $url))
		{
			return ["success" => false, "message" => "Invalid URL Format. Please use the following format: https://github.com/USERNAME/REPO"];
		}

		$client = new Client();

		try{
			$response = $client->request("GET", "$url/archive/$branch.zip");
		}catch(\Exception $e)
		{
			return ["success" => false, "message" => "Invalid URL.  Please check your spelling and try again."];
		}

		if($response->getStatusCode() != '200')
		{
			return ["success" => false, "message" => "Invalid URL.  Please check your spelling and try again."];
		}

		$filename = "";

		if($download)
		{
			$filename = storage_path()."/framework/cache/".md5($url)."_".time();
			file_put_contents($filename.".zip", $response->getBody());
		}

		return ["success" => true, "filename" => $filename];
	}

	public function listProjectBranches(Project $project, $asArray = false){
		
		if($project->ProjectType->name != "github")
		{
			return [];
		}

		if($project->repo != null)
		{
			return $this->listBranches($project->repo, $asArray);
		}

		$url = $project->url;

		$url = str_replace("https://github.com/", "", $url);


		$response = $this->client->request("GET", "/repos/$url/branches");

		if($response->getStatusCode() != '200')
		{
			return [];
		}

		$list = json_decode((string)$response->getBody(), $asArray);

		return $list;
	}

}