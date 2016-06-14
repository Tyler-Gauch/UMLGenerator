<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OAuth\OAuth2\Service\GitHub;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use Config;
use Auth;
use Log;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function login(){
        Log::info("In login page");
        $githubService = $this->getGithubService();
        $url = $githubService->getAuthorizationUri();
        return response()->view("auth.login", ["github"=>$url]);
    }

    public function loginWithGithub(Request $request){

        Log::info("In Login with GitHub");
        $code = $request->input("code", null);

        $githubService = $this->getGithubService();

        if($code != null)
        {
            Log::info("Retrieved Code");
            $token = $githubService->requestAccessToken($code);

            $result = json_decode($githubService->request("https://api.github.com/user"), true);
            $result["token"] = $token;

            Log::info("Data base find or create user");
            $user = $this->findOrCreateUser($result);

            Log::info("Logging in user");
            Auth::login($user, true);

            return redirect("/");
        }else{
            Log::info("No code provided");
            return response()->view("auth.login", ["github"=>$url]);
        }
    }

    private function getGithubService(){
        $config = Config::get("oauth-5-laravel.consumers.GitHub");

        $creds = new Credentials($config["client_id"], $config["client_secret"], "http://localhost/auth/github");

        return \OAuth::consumer("GitHub", $creds);
    }

    private function findOrCreateUser($user){
        if(($authUser = User::where("email", "=", $user["email"])->first()) != null){
            return $authUser;
        }else{
            return User::create([
                "name" => $user["name"],
                "email" => $user["email"],
                "provider_id" => $user["id"],
                "username" => $user["login"],
                "access_token" => $user["token"]->getAccessToken()
            ]);
        }
    }
}
