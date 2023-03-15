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
use App\Models\User;
use App\Models\SocialAccount;

class TwitchHelper
{
   public static function getModListForChannel($channel)
   {
       	$boomtv_user = User::where('name','boomtvmod')->where("type", User::USER_TYPE_TWITCH)->first();
    	$boomtv_social = SocialAccount::where("user_id", $boomtv_user->id)
    					->first();
    	$access_token = $boomtv_social->access_token;
        $nickname = $boomtv_user->name;
    	$sock = fsockopen('irc.twitch.tv', 6667);
        if ($sock) {
    	fwrite($sock,"PASS oauth:".$access_token."\r\n");
        fwrite($sock,"NICK ".$nickname."\r\n");

    	fwrite($sock,"JOIN #".$channel."\r\n");
    	fwrite($sock,"CAP REQ :twitch.tv/commands"."\r\n");
    	fwrite($sock,"PRIVMSG #".$channel." :.mods \r\n");
	
        $string = "The moderators of this room are:";
        $stringNotFound = "There are no moderators of this room";
        $status = 0;
        $moderator = "boomtvmod";
        $message = "Haven't ". $moderator ." moderator";
        $listMods = "";
    	while( $content = fgets($sock) ) 
    	{
    		$result = strpos($content, $string);
    		if($result)
    		{
				$status = 1;
				$message = "OK";
                $listMods = trim(substr($content, $result + strlen($string)));
    			break;
    		}
    		$resultNotFound = strpos($content, $stringNotFound);
    		if($resultNotFound)
    		{
    			break;
    		}
    	}
    	Log::info("checkChannelModerator success");
    	} 
    	else {
    		Log::error("checkChannelModerator error: chat twich fails");
    	}
    	fclose($sock);
    	return ['status'=> $status,'msg'=>$message, 'mods' => $listMods];
   }
   
   public static function postMessageToChannel($channel, $message) {
        $boomtv_user = User::where('name','boomtvmod')->first();
        $boomtv_social = SocialAccount::where("user_id", $boomtv_user->id)
        				->first();
        $access_token = $boomtv_social->access_token;
        $nickname = $boomtv_user->name;
        $sock = fsockopen('irc.twitch.tv', 6667);
        if ($sock) {
            fwrite($sock,"PASS oauth:".$access_token."\r\n");
            fwrite($sock,"NICK ".$nickname."\r\n");
            fwrite($sock,"JOIN #".$channel."\r\n");
            fwrite($sock,"PRIVMSG #".$channel." : " . $message ."\r\n");
            Log::info("PRIVMSG #".$channel." : " . $message . "\r\n");
            Log::info("TwitchHelper postMessageToChannel success!");
        } 
        else {
        	Log::error("TwitchHelper postMessageToChannel: failed");
        }
        fclose($sock);
   } 

}