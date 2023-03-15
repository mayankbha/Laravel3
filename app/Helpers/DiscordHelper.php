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

class DiscordHelper
{
    public static function createChannel($guildId)
    {
        $client = new Client();
        $url = config("services.discord.api_url");
        $botToken = config("services.discord.bot_token");
        $nameReplay = config("services.discord.channel_replay_name");
        try {
            $channels = $client->request('GET', $url . "/guilds/{$guildId}/channels", [
                'headers' => [
                    'Authorization' => "Bot " . $botToken,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $results = json_decode((string) $channels->getBody(), true);
            foreach ($results as $channel) {
                Log::info($channel);
                if($channel["name"] == $nameReplay)
                {
                    Log::info("replay channel existed");
                    return $channel["id"];
                }
            }
            $channelReplay = $client->request('POST', $url . "/guilds/{$guildId}/channels", [
                'headers' => [
                    'Authorization' => "Bot " . $botToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => ["name" => $nameReplay, "type" => "text"]
            ]);
            $resultReplay = json_decode((string) $channelReplay->getBody(), true);

            if(isset($resultReplay['id']))
            {
                return $resultReplay['id'];
            }
            else
            {
                Log::info("cannot create channel discord");
            } 
        }
        catch (ClientException $e)
        {
            Log::error("create channel discord error: " . $e->getMessage());
        }
        return 0;
    }
}