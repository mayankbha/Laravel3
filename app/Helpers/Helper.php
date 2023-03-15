<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App;
use Log;
use Mail;
use Route;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Client;
use SparkPost\SparkPost;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Carbon\Carbon;
use Abraham\TwitterOAuth\TwitterOAuth;

class Helper
{
    public static function sendMailBySparkPostTemplate($tempId, $data, $recipients, $cc)
    {
        try {
        $httpClient = new GuzzleAdapter(new Client());
        $sparky = new SparkPost($httpClient, ['key'=>config("services.sparkpost.secret"), 'async' => false]);

        $response = $sparky->transmissions->post([
            'content' => [
                'template_id' => 'test-2',
                'use_draft_template' => true
            ],
            'substitution_data' => $data,/*['name' => 'XoanXoan'],*/
            //dateSubject, imageHeader, imageProfile, username, date, numberBoom,
            //thumbnail, link
            'recipients' => $recipients,
            'cc' => $cc
            /*'recipients' => [
                [
                    'address' => [
                        'name' => 'xoan nguyen',
                        'email' => 'xoan.nt@boom.tv',
                    ],
                ],
            ],
            'cc' => [
                [
                    'address' => [
                        'name' => 'ANOTHER_NAME',
                        'email' => 'ANOTHER_EMAIL',
                    ],
                ],
            ],*/
        ]);
        
        Log::info("send mail with template_id status code: " . $response->getStatusCode());
            $data = $response->getBody();
            if (isset($data['results']['id'])){
                return true;
            }
        } catch (\Exception $e) {
             Log::info("send mail with template_id error: " . $e);
        }
        return false;
    }
    public static function sendRemindeMailBySparkPostTemplate($tempId, $data, $recipients, $cc)
    {
        try {
            $httpClient = new GuzzleAdapter(new Client());
            $sparky = new SparkPost($httpClient, ['key'=>config("services.sparkpost.secret"), 'async' => false]);
            //$sparky->setOptions(['async' => false]);

            $promise = $sparky->transmissions->post([
                'content' => [
                    'template_id' => $tempId,
                    'use_draft_template' => true,
                ],
                'recipients' => $recipients,
                'substitution_data' => $data
            ]);
            Log::info("[sendReminderEmail] template_id {$tempId} {$data['email']} status code: " . $promise->getStatusCode());
            $data = $promise->getBody();
            if (isset($data['results']['id'])){
                return $data['results']['id'];
            }
            return 0;

        } catch (\Exception $e) {
            Log::info("send mail with template_id error {$e->getTraceAsString()}");
            return 0;
        }
    }

    public static function sendMailChimpSubcribe($email, $twitch) {
        try {
            $client = new Client([
                    'auth'     => ['apikey', '3d803ef350521b87e2001c1342c07291-us15'],
            ]);
            $res = $client->post('https://us15.api.mailchimp.com/3.0/lists/22ad764148/members', ['json' => [
                "email_address" => $email,
                "status" => "subscribed",
                "merge_fields" => [
                    "MMERGE4" => $twitch
                ]
                ]]);
            Log::info("Response code from mailchimp: " . $res->getStatusCode());
            Log::info("Response body from mailchimp: " . $res->getBody());
        } catch (TransferException $e) {
            // If there are network errors, we need to ensure the application doesn't crash.
            // if $e->hasResponse is not null we can attempt to get the message
            // Otherwise, we'll just pass a network unavailable message.
            if ($e->hasResponse()) {
                $exception = (string) $e->getResponse()->getBody();
                //$exception = json_decode($exception);
                Log::error("exception: " . $exception);
            } else {
                Log::error("Error connect to mailchimp: " . $e->getMessage());
            }
        }
    }

    public static function sendMailChimpSubcribeVrbeta($email, $twitch) {
        try {
            $client = new Client([
                'auth'     => ['apikey', '3d803ef350521b87e2001c1342c07291-us15'],
            ]);
            $res = $client->post('https://us15.api.mailchimp.com/3.0/lists/22ad764148/members', ['json' => [
                "email_address" => $email,
                "status" => "subscribed",
                "merge_fields" => [
                    "MMERGE4" => $twitch
                ]
            ]]);
            Log::info("Response code from mailchimp: " . $res->getStatusCode());
            Log::info("Response body from mailchimp: " . $res->getBody());
        } catch (TransferException $e) {
            // If there are network errors, we need to ensure the application doesn't crash.
            // if $e->hasResponse is not null we can attempt to get the message
            // Otherwise, we'll just pass a network unavailable message.
            if ($e->hasResponse()) {
                $exception = (string) $e->getResponse()->getBody();
                //$exception = json_decode($exception);
                Log::error("exception: " . $exception);
            } else {
                Log::error("Error connect to mailchimp: " . $e->getMessage());
            }
        }
    }

    public static function sendMailChimpRegister($email, $twitch) {
        try {
            $client = new Client([
                    'auth'     => ['apikey', '3d803ef350521b87e2001c1342c07291-us15'],
            ]);
            $res = $client->post('https://us15.api.mailchimp.com/3.0/lists/7680aa42c2/members', ['json' => [
                "email_address" => $email,
                "status" => "subscribed",
                "merge_fields" => [
                    "MMERGE3" => $twitch
                ]
                ]]);
            Log::info("Response code from mailchimp register: " . $res->getStatusCode());
            Log::info("Response body from mailchimp register: " . $res->getBody());
        } catch (TransferException $e) {
            // If there are network errors, we need to ensure the application doesn't crash.
            // if $e->hasResponse is not null we can attempt to get the message
            // Otherwise, we'll just pass a network unavailable message.
            if ($e->hasResponse()) {
                $exception = (string) $e->getResponse()->getBody();
                //$exception = json_decode($exception);
                Log::error("exception: " . $exception);
            } else {
                Log::error("Error connect to mailchimp : " . $e->getMessage());
            }
        }
    }
    
    public static function is_timestamp($timestamp)
    {
       return ((string) (int) $timestamp === $timestamp)
       && ($timestamp <= PHP_INT_MAX)
       && ($timestamp >= ~PHP_INT_MAX);
       //&& (!strtotime($timestamp));
    }
    public static function getDurationLength($video_path)
    {
        $cmd = "ffprobe -v quiet -of csv=p=0 -show_entries format=duration $video_path";
        $time_seconds = shell_exec($cmd);
        return $time_seconds;
    }

    public static function createVideoThumbnail($video, $image, $size, $second) {
        $second = self::getDurationLength($video)/2;
		$ffmpeg = 'ffmpeg';
		// time to take screenshot at
		$interval = 5;
		$cmd = "$ffmpeg -itsoffset -$second -i $video -s $size -vcodec mjpeg -vframes 1 -an -f rawvideo $image";
		exec($cmd);
	}

	public static function validateFile($file)
	{
		$extension = $file->getClientOriginalExtension();
    	//validate input
    	$error = "";
    	if($extension != "mp4")
    	{
    		$error .= "File type is mp4. ";
    	}
    	return $error;
	}

    public static function validateImageFile($file)
    {
        $info = getimagesize($file->getRealPath());
        $image_type = $info[2];
        $error = "";
        if(!in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
        {
            $error = "The file is not a known image format.";
        }
        return $error;
    }
    
	public static function getTimeAgo($datetime)
	{
		$now = time(); // or your date as well
        $your_date = strtotime($datetime);
        $datediff = $now - $your_date;
        $view = "";
        if($datediff >= 30*24*60*60)
        {
            $view =  floor($datediff/(30*60*60*24)) . " months ago" ;
        }
        if($datediff < 30*24*60*60 && $datediff >= 24*60*60)
        {
            $view = floor($datediff/(60*60*24)) . " days ago" ;
        }
        if($datediff < 24*60*60 && $datediff >= 60*60)
        {
            $view = floor($datediff/(60*60)) . " hours ago" ;
        }
        if($datediff < 60*60)
        {
            $view = floor($datediff/(60)) . " minutes ago" ;
        }
        return $view;
	}

    
    public static function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        }
        elseif(preg_match('/OPR/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 

    /* hls player support
    Chrome for Android 34+
    Chrome for Desktop 34+
    Firefox for Android 41+
    Firefox for Desktop 42+
    IE11+ for Windows 8.1+
    Edge for Windows 10+
    Opera for Desktop
    Vivaldi for Desktop
    Safari for Mac 8+ (beta) */

    public static function checkBrowser()
    {
        $ua= Helper::getBrowser();
        $yourbrowser= "Your browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
        print_r($yourbrowser);
        
    }

    public static function sendMail($emailFrom, $emailTo, $subject, $info)
    {
        $data = ["emailFrom"=>$emailFrom, "emailTo"=>$emailTo, 
                "subject"=>$subject, "sender" => $info["sender"]];
        try 
        {
            Mail::send(["html" => $info["temp"]], $info, function ($message) use ($data){
                $message->from($data['emailFrom'], $data['sender']);
                $message->to($data['emailTo'])->subject($data['subject']);
            });
            Log::info("Send Mail Success to " . $emailTo . "with subject " . $data['subject']);
            return "Sending Successfully";
        } catch (\Exception $e) {
            Log::info("Send Mail Error");
            Log::info("Mail Info from: ".$emailFrom);
            Log::info("Mail Info to: ".$emailTo);
            Log::info("Subject: ".$subject);
            Log::error("Error: " . $e);
            return "Sending Fail. Please try it later";

        }
    }

    public static function sendMailCc($emailFrom, $emailTo,$emailCc, $subject, $info)
    {
        $data = ["emailFrom"=>$emailFrom, "emailTo"=>$emailTo,
            "subject"=>$subject, "sender" => $info["sender"] , "emailCc" => $emailCc ];
        try
        {
            Mail::send(["html"=>$info["temp"]], $info, function ($message) use ($data){
                $message->from($data['emailFrom'], $data['sender']);
                $message->to($data['emailTo'])->subject($data['subject']);
                $message->cc($data['emailCc'],$name = null);
            });
            Log::info("Send Mail Success to " . $emailTo . "with subject " . $data['subject']);
            return "Sending Successfully";
        } catch (\Exception $e) {
            Log::info("Send Mail Error");
            Log::info("Mail Info from: ".$emailFrom);
            Log::info("Mail Info to: ".$emailTo);
            Log::info("Subject: ".$subject);
            Log::error("Error: " . $e);
            return "Sending Fail. Please try it later";

        }
    }

    public static function returnInteger($value)
    {
        if(is_numeric($value))
        {
            $value = (int) $value;
            if(is_int($value)) return $value;
        }
        return 0;
    }

    public static function generate_twitch_subscribe_link($tw_username){
        return "https://www.twitch.tv/products/".$tw_username."/ticket/new";
    }

    public static function generate_twitch_profile_link($tw_username){
        return "https://www.twitch.tv/".$tw_username;
    }

    public static function generate_twitter_follow($username){
    return "https://twitter.com/intent/user?screen_name=" . $username;
    }

    public static function generate_facebook_follow($username){
        return "https://www.facebook.com/" . $username;
    }

    public static function generate_reddit_follow($username){
        return "https://www.reddit.com/user/" . $username;
    }

    public static function generate_facebook_share_link($url = ""){
        if ($url == ""){
            $url = route(Route::current()->getName());
        }
        $link = "https://www.facebook.com/sharer/sharer.php?quote=boomtv&u={$url}&src=sdkpreparse&hashtag=%23boomtv";
        return $link;
    }

    public static function generate_twitter_share_link($url = ""){
        if ($url == ""){
            $url = route(Route::current()->getName());
        }
        $link = "https://twitter.com/intent/tweet?hashtags=boomtv&original_referer={$url}&ref_src=&tw_p=tweetbutton&amp;url={$url}";
        return $link;
    }

    public static function event_stream_generate_quality($hls,$quality = ""){
        if ($quality != ""){
            $quality = "_" . $quality;
        }
        return str_replace("[quality]",$quality,$hls);
    }

    public static function generateCode($id)
    {
        $code= crypt($id.date("h").date("i").date("s"), 'rl');
        $random = rand(0,9);
        $code= str_replace([
            "!","@","#","$","%","^","&","*","(",")","-","=","\\",".",",","/","<",">","?","+","_","[","]","{","}",";",":"
            ], $random, $code);
        return $code;
    }

    public static function sortSize($value, $precision = 2)
    {   
        $kilo = 1000;
        $mega = $kilo * 1000;
        $giga = $mega * 1000;
        $tera = $giga * 1000;
    
        if (($value >= 0) && ($value < $kilo)) {
            return $value . '';

        } elseif (($value >= $kilo) && ($value < $mega)) {
            return round($value / $kilo, $precision) . ' K';

        } elseif (($value >= $mega) && ($value < $giga)) {
            return round($value / $mega, $precision) . ' M';

        } elseif (($value >= $giga) && ($value < $tera)) {
            return round($value / $giga, $precision) . ' G';

        } elseif ($value >= $tera) {
            return round($value / $tera, $precision) . ' T';
        } else {
            return $value . '';
        }
    }

    public static function createLoginUrl($type=""){
        $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
        return route('oauth',['is_claim'=>1,'source'=>0,'redirect_uri'=>$rediect_uri, "type" => $type]);
    }

    public static function createLoginUrlForSubscribe($type=""){
        $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
        $rediect_uri = $rediect_uri."&autosubscribe=1";
        return route('oauth',['is_claim'=>1,'source'=>0,'redirect_uri'=>$rediect_uri, "type" => $type]);
    }

    public static function createReloginUrl($rediect_uri){
        return route('oauth',['is_claim'=>1,'source'=>0,'redirect_uri'=>$rediect_uri]);
    }

    public static function checkUrlDomain($url = ""){
        $base_url = url('/');
        $base_parse = parse_url($base_url);
        $base_domain = $base_parse['host'];

        $url_parse = parse_url($url);

        $url_domain = isset($url_parse['host']) ? $url_parse['host'] : "";

        if ($url_domain == $base_domain){
            return true;
        }
        else{
            return false;
        }
    }

    public static function redirectHttpToHttps($request){
        if (config('app.env') == "production"){
            if ($request->server('HTTP_X_FORWARDED_PROTO') == "http"){
                $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
                return redirect()->to($rediect_uri);
            }
        }
    }

    public static function  urlCurrent(){
        $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
        return $rediect_uri;
    }

    public static function convertTimeZone($timestamp, $formatCurrent = "Y-m-d H:i:s", $format = 'F j, Y', $timezoneCurrent = "UTC", $timezone = "PST")
    {
        $date = Carbon::createFromFormat($formatCurrent, $timestamp, $timezoneCurrent);
        $d = $date->tz($timezone);
        return $d->format($format);  
    }

    public static function pageValidate($request){
        $page = $request->input('page');
        $page = intval($page);
        if ($page <= 0){
            $page = 1;
        }
        return $page;
    }

    public static function postTwitter($token, $tokenSecret, $postContent)
    {
        try
        {
            $key = config("services.twitter.client_id");
            $secret = config("services.twitter.client_secret");
            $connection = new TwitterOAuth($key, $secret, $token, $tokenSecret);
            $statues = $connection->post("statuses/update", ["status" => $postContent]);
            return true;
        } 
        catch (\Exception $e) 
        {
            Log::info("Post twitter error: " . $e->getMessage());
            return false;
        }
    }

    public static function isExpire($datetime)
    {
        $now = Carbon::now();
        $expire_in = Carbon::instance(new \DateTime($datetime));
        if ($expire_in < $now){
            return false;
        }
        return true;
    }

    public static function getRamdomTwitterShareText(){
        $text_dic = [
            'Check out this BOOMtastic replay 🔥',
            'You saw it here first 👀',
            'You know I always got those 🔥 replays',
            "You don't mess with the champ 🍾",
            "GG hold the re",
            "A dish best served cold",
            "EZ 😹",
            "gg lulz 😎",
            "Boom goes the replay 💥",
            "you are welcome! 😎",
            "What happened? Did I break it?",
            "oh wait check this out 👇",
            "its LIT 🔥",
        ];
        return $text_dic[array_rand($text_dic,1)];
    }
    public static function randomKey($length) {
        $pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
        $key = "";
        for($i=0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $key;
    }
}