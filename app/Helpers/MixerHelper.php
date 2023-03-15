<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App;
use Log;
use Mail;
use Route;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\SocialAccount;

class MixerHelper
{
   public static function getAuthorizationUrl($isMod = 0)
   {
        $redirectUrl = config("mixer.redirect_url");
        $scopes = implode(" ", config("mixer.scopes"));
        $clientId = config("mixer.client_id");
        $clientSecret = config("mixer.client_secret");
        $state = Str::random(40);
        $responseType = "code";
        if($isMod) $responseType = "token";
        $url = "https://mixer.com/oauth/authorize?response_type=".$responseType."&redirect_uri=$redirectUrl&scope=$scopes&client_id=$clientId&client_secret=$clientSecret&state=$state&force_verify=true";
        return $url;
   } 
   public static function getUserMixer($code, $dataMod)
   {
        $client = new Client();
        $info = array();
        Log::info("code mixer: " . $code);
        if(!$dataMod["is_mod"])
        {
            $redirectUrl = config("mixer.redirect_url");
            $clientId = config("mixer.client_id");
            $clientSecret = config("mixer.client_secret");
            $response = $client->post('https://mixer.com/api/v1/oauth/token', [
                    'form_params' => [
                        'grant_type' => 'authorization_code',
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret, 
                        'redirect_uri' => $redirectUrl,
                        'code' => $code // Get code from the callback
                    ],
                ]);
            $results = json_decode((string) $response->getBody(), true);
            Log::info("Login with mixer: ");
            Log::info($results);
            $info["token"] = $results["access_token"];
            $info["refreshToken"] = $results["refresh_token"];
            $info["expiresIn"] = $results["expires_in"];
            $info["tokenType"] = $results["token_type"];
        }
        else
        {
            $info["token"] = $dataMod["access_token"];
            $info["refreshToken"] = "";
            $info["expiresIn"] = $dataMod["expires_in"];
            $info["tokenType"] = $dataMod["token_type"];
        }
        Log::info("info get users " . $info["tokenType"] ." ". $info["token"]);
        $responseUser = $client->request('GET', 'https://mixer.com/api/v1/users/current', [
            'headers' => [
                  'Authorization' => $info["tokenType"] ." ". $info["token"],
            ],
        ]);
        $resultsUser = json_decode((string) $responseUser->getBody(), true);
        Log::info($resultsUser);
        $info["name"] = $resultsUser["username"]; 
        $info["nickname"] = $resultsUser["username"]; 
        $info["email"] = $resultsUser["email"]; 
        $info["avatar"] = $resultsUser["avatarUrl"]; 
        $info["id"] = $resultsUser["channel"]["userId"]; 
        $info["channelId"] = $resultsUser["channel"]["id"];
        return $info;
   }

    public static function chatMixerWithBot($channelName, $social, $text)
    {
        // get channel id
        $client = new Client();
        $channel = $client->request('GET', 'https://mixer.com/api/v1/channels/'.$channelName.'?fields=id');
        $id = json_decode((string) $channel->getBody(), true)["id"];
        // get authkey
        $chat = $client->request('GET', 'https://mixer.com/api/v1/chats/'.$id, [
            'headers' => [
                'Authorization' => $social->token_type.' '.$social->access_token,
            ]
        ]);
        $results = json_decode((string) $chat->getBody(), true);
        if(Helper::isExpire($social->expire_in))
        {
            $social = MixerHelper::refreshToken($social);
            $chat = $client->request('GET', 'https://mixer.com/api/v1/chats/'.$id, [
                'headers' => [
                    'Authorization' => $social->token_type.' '.$social->access_token,
                ]
            ]);
            $results = json_decode((string) $chat->getBody(), true);
        }
        //chat msg
        $arrRequest = ["type" => "method", "method" => "auth",
                        "arguments" => [
                            $id,
                            $social->social_id,
                            $results["authkey"]
                        ], "id" => 0];
        $jsonRequest = json_encode($arrRequest);

        $arrRequestMsg = ["type" => "method", "method" => "msg",
                        "arguments" => [
                            $text
                        ], "id" => 0];
        $jsonRequestMsg = json_encode($arrRequestMsg);
        $client = new \WebSocket\Client($results["endpoints"][0]);
        $client->send($jsonRequest);
        $client->send($jsonRequestMsg);
        $client->receive();
        $client->receive();
        $client->receive();
    }

    public static function refreshToken($social)
    {
        try {
            if(Helper::isExpire($social->expire_in))
            {
                $client = new Client();
                $redirectUrl = config("mixer.redirect_url");
                $clientId = config("mixer.client_id");
                $clientSecret = config("mixer.client_secret");
                $response = $client->post('https://mixer.com/api/v1/oauth/token', [
                        'form_params' => [
                            'grant_type' => 'refresh_token',
                            'client_id' => $clientId,
                            'client_secret' => $clientSecret, 
                            'redirect_uri' => $redirectUrl,
                            'refresh_token' => $social->refresh_token // Get code from the callback
                        ],
                    ]);
                $results = json_decode((string) $response->getBody(), true);
                Log::info("refresh token info: ");
                Log::info($results);
                return SocialAccount::updateAccessToken($social, $results);
            }
        }
        catch (ClientException $e)
        {
            Log::error("refresh token for mixer error: " . $e->getMessage());
        }
        return null;
    }

    public static function getCurrentUser($social)
    {
        try {
            $mixer = $social;
            if(Helper::isExpire($social->expire_in))
            {
                $socialUpdate = MixerHelper::refreshToken($social);
                if($socialUpdate != null)  $mixer = $socialUpdate;
            }
            if($mixer != null)
            {
                $client = new Client();
                $responseUser = $client->request('GET', 'https://mixer.com/api/v1/users/current', [
                    'headers' => [
                          'Authorization' => $mixer->token_type ." ". $mixer->access_token,
                    ],
                ]);
                $resultsUser = json_decode((string) $responseUser->getBody(), true);
                $responseUserFollowing = $client->request('GET', 'https://mixer.com/api/v1/users/'.$resultsUser["id"].'/follows');
                $resultsUserFollowing = json_decode((string) $responseUserFollowing->getBody(), true);
                $resultsUser["followings"] = count($resultsUserFollowing);
                return $resultsUser;
            }
        }
        catch (ClientException $e)
        {
            Log::error("get user for mixer error: " . $e->getMessage());
        }
        return null;
    }

    public static function checkChannelModerator($channelName, $socialMod)
    {
        try {
            // get channel id
            $client = new Client();
            $channel = $client->request('GET', 'https://mixer.com/api/v1/channels/'.$channelName.'?fields=id');
            $id = json_decode((string) $channel->getBody(), true)["id"];
            // get authkey
            $chat = $client->request('GET', 'https://mixer.com/api/v1/chats/'.$id, [
                'headers' => [
                    'Authorization' => $socialMod->token_type.' '.$socialMod->access_token,
                ]
            ]);
            $results = json_decode((string) $chat->getBody(), true);
            $roles = $results["roles"];
            if(in_array("Mod", $roles))
            {
                return true;
            }
        }
        catch (ClientException $e)
        {
            Log::error("check mod for mixer error: " . $e->getMessage());
        }
        return false;
    }

    public static function getChannelInfo($channelId)
    {
        try {
            // get channel id
            $client = new Client();
            $channel = $client->request('GET', 'https://mixer.com/api/v1/channels/'.$channelId);
            $results = json_decode((string) $channel->getBody(), true);
            return $results;
        }
        catch (ClientException $e)
        {
            Log::error("check mod for mixer error: " . $e->getMessage());
        }
        return false;
    }
}