<?php
/**
 * Created by PhpStorm.
 * User: tannn
 * Date: 8/15/17
 * Time: 7:24 AM
 */
namespace App\Helpers;
use App\Models\SocialAccount;
use Youtube;

class YoutubeHelper
{
    public static function getChannelBroadcastStatus($channel_id){
        $social_acount = SocialAccount::where("channel_id",$channel_id)->first();
        return Youtube::getChannelBroadcastStatus($social_acount);
    }

    public static function getCurrentViewer($channel_id){
        $social_acount = SocialAccount::where("channel_id",$channel_id)->first();
        $viewer_count =  Youtube::getChannelCurrentViewersList($social_acount);
        return intval($viewer_count);
    }

}