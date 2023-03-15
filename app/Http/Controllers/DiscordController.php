<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Socialite;
use Session;
use GuzzleHttp;
use App\Models\DiscordInfo;
use App\Models\Token;
use Log;
use Lang;
use App\Helpers\DiscordHelper;

class DiscordController extends Controller
{
    //
    public function showLogin(Request $request){
        $token = Session::get('token');
        if (is_object($token)){
            $token = $token['access_token'];
        }
        else{
            $token = "";
        }
        return view('user.discord_login',['token'=>$token]);
    }

    public function loginToDiscord(Request $request){
        $token = $request->input('token');
        Session::put('token', $token);
        $userToken = Token::where("token", $token)->first();
        if ($userToken != null){
            $userId =  $userToken->user_id;
            Session::put('dc_user_id', $userId);

            return Socialite::with('discord')->with(['scope'=>'bot identify guilds email','permissions'=>523344])->redirect();
        }
        else
        {
            $login_url = route("oauth",['redirect_uri'=> route('discordapp.login')]);
            return redirect($login_url);
        }

    }

    public function redirectUriHanler(Request $request){
        $token = Session::pull('token');
        $userToken = Token::where("token", $token)->first();
        if ($userToken == null){
            $login_url = route("oauth",['redirect_uri'=> route('discordapp.login')]);
            return redirect($login_url);
        }
        $error = $request->input('error');
        $link = route('homepage', ['view' => 'popular']);
        if ($error != ""){
            //return view('errors.discord_error');
            $link = $link . "?status=1&error=".$error ;
            return redirect()->to($link);
        }
        else{
            try{
                $guild_id = $request->input('guild_id');
                $user_id = Session::pull('dc_user_id');
                $user = Socialite::driver('discord')->user();
                $accessTokenResponseBody = $user->accessTokenResponseBody;
                $discord_info = DiscordInfo::where('user_id',$user_id)->first();
                if (!$discord_info){
                    $discord_info = new DiscordInfo();
                    $discord_info->user_id = $user_id;
                    $discord_info->access_token = $accessTokenResponseBody['access_token'];
                    $discord_info->token_type = $accessTokenResponseBody['token_type'];
                    $discord_info->expire_in = Carbon::now()->addSeconds($user->expiresIn);
                    $discord_info->refresh_token = $accessTokenResponseBody['refresh_token'];
                    $discord_info->scope = $accessTokenResponseBody['scope'];
                    $discord_info->discord_id = $user->id;
                    $discord_info->nickname = $user->nickname;
                    $discord_info->username = $user->name;
                    $discord_info->email = $user->email;
                    $discord_info->avatar = $user->avatar == null ? "" : $user->avatar;
                    $discord_info->guild_id = $guild_id;
                    $discord_info->save();
                }
                else{
                    $discord_info->user_id = $user_id;
                    $discord_info->access_token = $accessTokenResponseBody['access_token'];
                    $discord_info->token_type = $accessTokenResponseBody['token_type'];
                    $discord_info->expire_in = Carbon::now()->addSeconds($user->expiresIn);
                    $discord_info->refresh_token = $accessTokenResponseBody['refresh_token'];
                    $discord_info->scope = $accessTokenResponseBody['scope'];
                    $discord_info->discord_id = $user->id;
                    $discord_info->nickname = $user->nickname;
                    $discord_info->username = $user->name;
                    $discord_info->email = $user->email;
                    $discord_info->avatar = $user->avatar == null ? "" : $user->avatar;
                    $discord_info->guild_id = $guild_id;
                    $discord_info->save();
                }
                $idChannel = DiscordHelper::createChannel($discord_info->guild_id);
                if($idChannel > 0)
                {
                    $discord_info->replay_channel_id = $idChannel;
                    $discord_info->save();
                }
                Log::info("[discord] success add boomtv bot to $user->name channel");
                Session::put('discord_msg',Lang::get('discord.add_success',['name'=>$user->name]));
                $link = $link . "?status=0";
                return redirect()->to($link);
            }
            catch (\Exception $exception){
                Log::info($exception->getMessage());
                //return view('errors.discord_error');
                $link = $link . "?status=1&error=discord_login_error";
                return redirect()->to($link);
            }

        }

    }

    public function getInfo(){
        $token = Session::get('token');
        $token = $token['access_token'];
        $client_id = config('services.discord.client_id');
        $client = new GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://discordapp.com/',
            // You can set any number of default request options.
            'timeout'  => 5.0,
        ]);
        $response = $client->get("/api/users/@me/guilds",[
            'headers' => [
                'Authorization' => "Bearer {$token}"
        ]]);

        $body = $response->getBody();

        $body = json_decode($body,true);

        $list_user_server = new Collection($body);
        $list_user_server = $list_user_server->keyBy('id');


        $response = $client->get("/api/users/@me/guilds",[
            'headers' => [
                'Authorization' => "Bot MzIzNjY2NDM5MDgyODY4NzM2.DB_djw.JrnTLquif9QC0TBE84qQ_78FG7k"
            ]]);

        $body = $response->getBody();
        $body = json_decode($body,true);
        $list_bot_server = new Collection($body);
        $list_bot_server = $list_bot_server->keyBy('id');
        $data = new Collection();
        foreach ($list_user_server as $key=>$item){
            if ($list_bot_server->get($key)){
                $data->add($item);
            }
        }

        dd ($data->get(0));
    }
}
