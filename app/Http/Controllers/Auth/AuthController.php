<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Token;
use App\Models\SocialAccount;
use App\Models\SocialConnected;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Socialite;
use Session;
use TwitchApi;
use Crypt;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use Log;
use Str;
use App\Helpers\MixerHelper;
use Redirect;

use Ixudra\Curl\Facades\Curl;

class AuthController extends Controller
{

    /**
     * @SWG\Post(path="/oauth",
     *   tags={"api"},
     *   summary="login with twitch",
     *   description="Return url and params. If success, return status param = 0 and token. If fail, return status <> 0 and error",
     *   operationId="redirectToProvider",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public $codeClaim;
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR_LOGIN = 1;
    const STATUS_ERROR_CLAIM = 2;
    const STATUS_ERROR_NOT_STREAMER = 3;

    public function genCode()
    {
        $code = Str::random(30);
        $this->codeClaim = $code;
    }

    public function redirectToProvider(Request $request)
    {
        $type = $request->input('type');
        $isMod = $request->input('is_boom_mod');
        $isClaim = $request->input('is_claim');
        $username = $request->input('username');
        $source = $request->input('source');
        $language = $request->input('language');
        $redirect_uri = $request->input('redirect_uri');
        Log::info("Login info: type $type - is_claim $isClaim - username $username - source $source - language $language - redirect_uri: $redirect_uri");
        $request->session()->put('isClaim' . $this->codeClaim, $isClaim);
        $request->session()->put('username' . $this->codeClaim, $username);
        $request->session()->put('source' . $this->codeClaim, $source);
        if (!Helper::checkUrlDomain($redirect_uri)){
            $redirect_uri = null;
        }
        $request->session()->put('redirect_uri',$redirect_uri);
        $isSetLang = true;
        if(!isset($language) || $language == "" || $language == null)
        {
            $isSetLang = false;
        }
        if(!isset($type) || $type == "" || $type == null)
        {
            $type = "twitch";
        }
        if(!isset($isMod) || $isMod == "" || $isMod == null)
        {
            $isMod = 0;
        }
        $request->session()->forget('account_type');
        $request->session()->put('account_type', $type);
        $request->session()->forget('is_mod');
        $request->session()->put('is_mod', $isMod);
        if($type == "mixer")
        {
            $url = MixerHelper::getAuthorizationUrl($isMod);
            return Redirect::to($url);
        }
        
		if($type == "youtube")
        {
			// Using static url with dynamic parameters to get refresh token
			$url = "https://accounts.google.com/o/oauth2/auth?client_id=".config('youtube.client_id')."&redirect_uri=".config('youtube.routes.redirect_uri')."&scope=https://www.googleapis.com/auth/plus.me+https://www.googleapis.com/auth/userinfo.email+https://www.googleapis.com/auth/youtube+https://www.googleapis.com/auth/youtube.upload+https://www.googleapis.com/auth/youtube.readonly+https://www.googleapis.com/auth/youtube.force-ssl+https://www.googleapis.com/auth/userinfo.profile&response_type=code&access_type=offline&approval_prompt=force";

			return Redirect::to($url);
        }
		
       if ($type == "twitch" || $type == "facebook" || $type == "google" || $type == "twitter") {
            
            if($isSetLang)
            {
            return Socialite::with($type)->with(["language"=> $language])->redirect();
            }
            else
            {
                return Socialite::with($type)->redirect();
            }
        } else {
            $request->session()->forget('acount_type');
            $request->session()->put('acount_type', 'twitch');
            if($isSetLang)
            {
                return Socialite::with($type)->with(["language"=> $language])->redirect();
            }
            else
            {
                return Socialite::with($type)->redirect();
            }
        }
    }


    public function handleProviderCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            $error = $request->input('error');
            $error_description = $request->input('error_description');
            $link = route('homepage', ['view' => 'popular']);

            if ($error == "") {

               //$account_type = $request->session()->get('account_type', 'twitch');
                $account_type = $request->session()->get('account_type');

                $claimForUser = false;
                $claimForUserSuccess = false;
                $now = new \DateTime();
                $token = Crypt::encrypt(rand(0, 128) . $now->getTimestamp());
                $account = array();
                if($account_type == "mixer")
                {
                    $isMod = $request->session()->get('is_mod', 0);
                    $dataMod = array("is_mod" => false);
                    if($isMod == 1)
                    {
                        $code = "";
                        $dataMod["is_mod"] = true;
                        Log::info("data boommod");
                        Log::info($dataMod);
                    }
                    $account = MixerHelper::getUserMixer($code, $dataMod);
                }
                else
                {
                    $accountObj = Socialite::driver($account_type)->stateless()->user();
					//echo "<pre>"; print_r($accountObj); die;

					if($account_type == 'youtube') {
						$user_details_json = Curl::to('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$accountObj->token)->get();

						$user_details_json_decode = json_decode($user_details_json);

						/*if($user_details_json_decode->name != '')
                                                                                            $name = strtolower(str_replace(' ', '', $user_details_json_decode->name));
                                                                                        else
                                                                                            $name = strtolower(str_replace(' ', '', $accountObj->nickname));*/

                                                                                        $name = $accountObj->id;
						$email = $user_details_json_decode->email;
					} else {
						$name = $accountObj->name;
						$email = $accountObj->email;
					}
			
					//$user_details = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$accountObj->token);

					//echo "<pre>"; print_r(json_decode($user_details)); die;

					//$accessTokenResponseBody = $accountObj->accessTokenResponseBody;
					//echo "<pre>"; print_r($accessTokenResponseBody); die;

                    $account["id"] = $accountObj->id;
                    $account["name"] = $name;
                    $account["nickname"] = $accountObj->nickname;
                    $account["email"] = $email;
                    $account["token"] = $accountObj->token;
                    $account["tokenType"] = "";
                    if($account_type == 'youtube')
                        $channelId = $accountObj->id;
                    else
                        $channelId = "";
                    $account["channelId"] = $channelId;
                    if($account_type=="twitter") {
                        $account["tokenSecret"] = $accountObj->tokenSecret;
                    }
                    else
                    {
                        $account["refreshToken"]=$accountObj->refreshToken;
                        $account["expiresIn"]=$accountObj->expiresIn;
                    }
                    $account["avatar"] = $accountObj->avatar;
                }
                
                $isClaim = 0;
                $isStreamer = 0;
                if ($request->session()->has('isClaim' . $this->codeClaim)) {
                    $isClaim = $request->session()->get('isClaim' . $this->codeClaim, 0);
                }
                // claim user
                if ($request->session()->has('username' . $this->codeClaim)) {
                    $claimForUser = true;
                    $username = $request->session()->get('username' . $this->codeClaim, "");

                    if ($isClaim && $account["name"] == $username) {
                        $claimForUserSuccess = true;
                    }
                }
                if ($isClaim && $claimForUser && !$claimForUserSuccess) {
                    $link = $link . "?status=" . AuthController::STATUS_ERROR_CLAIM;
                    return redirect()->to($link);
                }
                $source = User::SOURCE_APP;
                if($request->session()->has('source' . $this->codeClaim))
                {
                    $source = $request->session()->get('source' . $this->codeClaim, User::SOURCE_WEB);
                    if(!in_array($source, [User::SOURCE_APP, User::SOURCE_WEB, User::SOURCE_VR_APP]))
                    {
                        $source = User::SOURCE_WEB;
                    }
                }
                if($source == User::SOURCE_APP) $isStreamer = 1;
                $user = User::createOrUpdateAccount($account,
                    array("account_type" => $account_type,
                        "is_claim" => $isClaim,
                        "token" => $token,
                        "claimForUser" => $claimForUser,
                        "source" => $source,
                        "isStreamer" => $isStreamer));

                // redirect to link for app
                $redirect_uri = $request->session()->pull('redirect_uri');
                if (isset($redirect_uri)){
                    $link = $redirect_uri;
                }
                else{
                    $link = route('homepage', ['view' => 'myfeed']);
                }


                if ($user != null) {

                    // streamer login
                    $user->is_claim = $isClaim;
                    $user->save();
                    $request->session()->forget('isClaim' . $this->codeClaim);
                    $request->session()->forget('username' . $this->codeClaim);
                    $request->session()->forget('source' . $this->codeClaim);
                    // login web
                    
                    if (($account_type == "twitch" || $account_type == "mixer" || $account_type == "youtube") && $isClaim == 0) {
                        $tokenName = "twitch_token";
                        if ($account_type == "mixer") {
                            $tokenName = "mixer_token";
                        }
		    	if ($account_type == "youtube") {
                            $tokenName = "youtube_token";
                        }
                        auth()->login($user, false);
                        $link = $link . "?status=" . AuthController::STATUS_SUCCESS . "&token=" . $token . "&".$tokenName."=" . $account["token"];
                    }
                    else{
                        auth()->login($user,true);
                    }
                    User::flushLoginInfo($user->id);

                } else {
                    $link = $link . "?status=" . AuthController::STATUS_ERROR_NOT_STREAMER;
                }

            } else {
                $link = $link . "?status=" . AuthController::STATUS_ERROR_LOGIN . "&error=" . $error;
                Log::info("Login error description: " . $error_description);
            }
            return redirect()->to($link);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e);
            return view('errors.relogin');
        }


    }

    public function loginToAfkvrAdmin(Request $request){
        if (auth()->id()){
            if ($this->is_admin_user){
                Session::put('is_user_login_admin',1);
                return redirect()->route('admin');
            }
            else{
                return view('errors.403');
            }
        }
        else{
            return redirect()->route('relogin');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->to(route('home', ['view' => 'popular']));
    }
    // twitter_streamer
    // connect twitter for streamer
    public function connectTwitter(Request $request)
    {
        // input request: token of streamer => save session
        $token = $request->input('token');
        $type = $request->input('type');
        $request->session()->forget('token');
        $request->session()->put('token', $token);
        $request->session()->forget('type');
        $request->session()->put('type', $type);
        return Socialite::with('twitter')->redirect();
    }
    public function callbackTwitter(Request $request)
    {
       // save access token, info streamer-twitter 
         try {
            $code = $request->input('code');
            $error = $request->input('error');
            $errorDescription = $request->input('error_description');
            $link = route('homepage', ['view' => 'popular']);
            if ($error == "") 
            {
                $token = $request->session()->get('token', '');
                $type = $request->session()->get('type', 'twitter');
                if($type == null) $type = "twitter";
                $userToken = Token::where("token", $token)->first();
                if($userToken != null)
                {
                    $userSocial = Socialite::driver("twitter")->user();
                    $socialConnected = SocialConnected::where("user_id", 
                                $userToken->user_id)
                                ->where("type", $type)->first();

                    if($socialConnected == null) $socialConnected = new SocialConnected();
                    $socialConnected->user_id = $userToken->user_id;
                    $socialConnected->type = $type;
                    $socialConnected->token = $userSocial->token;
                    $socialConnected->token_secret = $userSocial->tokenSecret;
                    $socialConnected->email = $userSocial->email;
                    $socialConnected->name = $userSocial->name;
                    $socialConnected->nick_name = $userSocial->nickname;
                    $socialConnected->avatar = $userSocial->avatar;
                    $socialConnected->social_id = $userSocial->id;
                    $socialConnected->save();
                    $link = $link . "?status=" . AuthController::STATUS_SUCCESS;
                    User::flushLoginInfo($userToken->user_id);
                }
            } 
            else 
            {
                $link = $link . "?status=" . AuthController::STATUS_ERROR_LOGIN . "&error=" . $error;
                Log::info("Login error description: " . $errorDescription);
            }
            return redirect()->to($link);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e);
            return view('errors.relogin');
        }
    }

}
