<?php 

namespace App\Helpers;
use App;
use Log;
use Mail;
use Route;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Client;
use App\Models\Video;
use File;

class FFmpegHelper
{
    public static function downloadVideo($link, $userId, $timeCut = 2)
    {
        $montage = storage_path('montage');
        if(!is_dir($montage))
        {
            mkdir($montage);
        }
        $path = $montage."/".$userId."/";
        $pathinfo = pathinfo($link);
        $videoPath = $path . $pathinfo["basename"]; 
        if(!is_dir($path))
        {
             mkdir($path);
        }
        $command = "cd " . $path . " && wget " . $link;
        exec($command);
        
        if(file_exists($videoPath)) {
            $videoPathReEncode = FFmpegHelper::reEncode($path, $videoPath, $timeCut);
            return $videoPathReEncode;
            return $videoPath;
        }
        else return false;
    }
    public static function convertVideoCount($videoSource)
    {
        $folderSource = public_path()."/montage_video/";
        $ffprobe = "ffprobe -v quiet -print_format json -show_format -show_streams " . $videoSource;
        $out = shell_exec($ffprobe);
        $outArr = json_decode($out, true);
        $size = $outArr["streams"][0]["width"]."x".$outArr["streams"][0]["height"];
        $audioHz = $outArr["streams"][1]["sample_rate"];
        $sar = $outArr["streams"][0]["sample_aspect_ratio"];
        $saveVideoCount = storage_path()."/montage/count_".$size."_".$audioHz;
        if(!is_dir($saveVideoCount))
        {
            mkdir($saveVideoCount);
            for($i=1 ; $i <= 10; $i++)
            {
                $fileInput = $folderSource."orange".$i.".mp4";
                $fileOut = $saveVideoCount."/orange".$i.".mp4";
                $ffmpeg = "ffmpeg -i ".$fileInput." -vcodec libx264 -crf 27 -g 15 -preset veryfast -pix_fmt yuv420p -profile main -r 30 -x264opts sar=1/1 -vf scale=".$size." -ar ".$audioHz." -codec:a aac  -map 0:1 -map 0:0 ".$fileOut;
                echo $ffmpeg."\n";
                exec($ffmpeg);
            }
        }
        //create audio silent
        $audioHzSilent = storage_path()."/montage/audioHz/";
        if(!is_dir($audioHzSilent))
        {
            mkdir($audioHzSilent);
        }
        $pathAudio3 = storage_path()."/montage/audioHz/silent3_".$audioHz.".mp3";
        $pathAudio2 = storage_path()."/montage/audioHz/silent2_".$audioHz.".mp3";
        if(!file_exists($pathAudio3)) 
        {
            $ffmpegAudio = "ffmpeg -f lavfi -i anullsrc=r=".$audioHz.":cl=mono -t 3 -q:a 9 -acodec libmp3lame ".$pathAudio3;
            exec($ffmpegAudio);
        }
        if(!file_exists($pathAudio2)) 
        {
            $ffmpegAudio = "ffmpeg -f lavfi -i anullsrc=r=".$audioHz.":cl=mono -t 2 -q:a 9 -acodec libmp3lame ".$pathAudio2;
            exec($ffmpegAudio);
        }
        $arr = ["path" => $saveVideoCount, "size" => $size, "pathAudio3" => $pathAudio3, "pathAudio2" => $pathAudio2, "sar" => $sar];
        return $arr;
    }
    public static function addTextForVideo($userId, $videoPath, $requestedBy, $views, $likes)
    {
        $montage = storage_path('montage');
        $path = $montage."/".$userId."/";
        $font = public_path("/fonts/AvantGardeDemiBT.ttf");
        $pathinfo = pathinfo($videoPath);
        $output = $pathinfo["dirname"]."/".$pathinfo["filename"]."_addText.".$pathinfo["extension"];
        $text = "";
        if($requestedBy != "")
        {
           $text .= "Requested by " . $requestedBy; 
        }
        if($views > 0)
        {
            $text .= " Views " . $views;
        }
        if($likes > 0 )
        {
            $text .= " Likes " . $likes;
        }
         
        $ffmpeg = 'ffmpeg -i '.$videoPath.' -vf drawtext="fontfile='.$font.': text=\''.$text.'\':fontcolor=#E87D32: fontsize=25: box=1: boxcolor=black@0.5: \
boxborderw=5:x=(w-tw-10):y=10" ' . $output;
        exec($ffmpeg);
        if(file_exists($output)) 
        {
            $file = fopen($path."list.txt","a");
            fwrite($file, $output . "\n");
            fclose($file);
            //unlink($videoPath);
            return true;
        }
        return false;
    }
    public static function addImageBeforeVideo($path, $videoCount, $videoPath, $requestedBy, $size, $audio, $sar)
    {
        $text = "nominated by " . $requestedBy;
        $pathVideoCount = $videoCount;
        $fontImpact = public_path("/fonts/impact.ttf");
        $fontHel = public_path("/fonts/HelveticaNeueMedium.ttf");
        if($requestedBy != "")
        {
            $pathVideoCount = $path . "count".time().".mp4";
            $pathBgNominated = public_path("/montage_video/bg_nominated.png");
            $pathVideoBgNm = $path."videoBg.mp4";
            //create video nominated
            //create image
            $pathNm = $path."nominatedImageText.jpg";
            $resizeBg = ' ';
            if($size !="1920x720")
            {
                $resizeBg = ' -resize '.$size;
            }
            $cmdCreateImageText = 'convert  '.$pathBgNominated.$resizeBg.'  -font '.$fontHel.' -pointsize 40 -kerning 4 -fill white -gravity center -annotate -260-80 "Nominated by" -font '.$fontHel.' -pointsize 100 -kerning 4 -fill white -gravity center -annotate  +50+0 "'.$requestedBy.'" -append +repage -quality 90 '.$pathNm;
            exec($cmdCreateImageText);
            // create video
            $pathVideoNm = $path."videoNm.mp4";
            /*$ffmpegAddAudio = "ffmpeg -loop 1 -i ".$pathNm." -i ".$audio." -shortest -c:v libx264 -c:a copy -vf scale=".$size." ".$pathVideoNm;*/
            $ffmpegAddAudio = "ffmpeg -loop 1 -i ".$pathNm." -i ".$audio." -c:v libx264 -vf scale=".$size.",setsar=".$sar." -t 2.0 ".$pathVideoNm;
            exec($ffmpegAddAudio);
            /*$ffmpegBg = "ffmpeg -loop 1 -i ".$pathBgNominated." -i ".$audio." -shortest -c:v libx264 -c:a copy -vf scale=".$size." ".$pathVideoBgNm;
            exec($ffmpegBg);
            $pathVideoNm = $path."videoNm.mp4";
            $ffmpegAddTextNm = 'ffmpeg -i '.$pathVideoBgNm.' -vf "drawtext=fontfile='.$fontHel.': text=\'Nominated by\': x=(w-text_w)/2-200: y=(h-text_h)/2-85: fontsize=40:fontcolor=#ffffff,drawtext=fontfile='.$fontHel.': text=\''.$requestedBy.'\': x=(w-text_w)/2: y=(h-text_h)/2: fontsize=100:fontcolor=#ffffff" '.$pathVideoNm;
            exec($ffmpegAddTextNm);*/
            // merge video nominated and video count
            $cmd = 'ffmpeg -i '.$videoCount.' -i '.$pathVideoNm.' -filter_complex "[0:v:0][0:a:0][1:v:0][1:a:0] concat=n=2:v=1:a=1 [v] [a]" -map [v] -map [a] -profile:v main -preset veryfast -r 30 ' . $pathVideoCount;
            exec($cmd);
            if(file_exists($pathNm)) 
            {
                unlink($pathNm);
            }
            if(file_exists($pathVideoNm)) 
            {
                unlink($pathVideoNm);
            }
        }
        // concat video count and video user
        $out = $path . time().".mp4"; 
        $cmd = 'ffmpeg -i '.$pathVideoCount.' -i '.$videoPath.' -filter_complex "[0:v:0][0:a:0][1:v:0][1:a:0] concat=n=2:v=1:a=1 [v] [a]" -map [v] -map [a] -profile:v main -preset veryfast -r 30 ' . $out;
        exec($cmd);
        if(file_exists($out)) 
        {
            unlink($videoPath);
            $file = fopen($path."list.txt","a");
            fwrite($file, $out . "\n");
            fclose($file);
        }

    }

    public static function generateConcatCmd($fileList, $pathMontage)
    {
        $cmd = "ffmpeg ";
        $content = File::get($fileList);
        $count = count($content);
        $filter = "'";
        $contents = explode("\n", $content);
        $n = 0;
        foreach($contents as $key => $line) {
            if($line != "")
            {
                $n++;
                $cmd .= " -i '".$line."'";

                $filter .= "[".$key.":v:0]"."[".$key.":a:0]";
            }
        }
        // filter
        $cmd .= " -filter_complex " . $filter."concat=n=".$n.":v=1:a=1 [v][a]' -map [v] -map [a] -profile:v main -preset veryfast -r 30 -y " . $pathMontage;
        return $cmd;

    }
    public static function createIntro($textIntro, $path, $size="1280x720", $avatar, $thumnail, $audio)
    {
        $videoBackground = $path."videoBackground.mp4";
        $fontImpact = public_path("/fonts/impact.ttf");
        $fontHel = public_path("/fonts/HelveticaNeueMedium.ttf");
        $thumbPath = $path."thumnail.jpg";
        $thumbPathTrans = $path."thumnail_trans.jpg";
        //download thumnail
        $wgetThumnail = "wget $thumnail -O " . $thumbPath;
        exec($wgetThumnail);
        exec("convert ".$thumbPath."  -fill black -colorize 60%  ".$thumbPathTrans);
        $thumbPath = $thumbPathTrans;
        //download avatar
        $avatarPath = $path."avatar.png";
        $avatarPathBd = $path."avatar_border.png";
        if(!file_exists($avatarPathBd)) {
            $wgetAvatar = "wget $avatar -O " . $avatarPath;
            exec($wgetAvatar);
            if (!file_exists($avatarPath)){
                $avatarPath = public_path().'/assets/'.config('content.assets_ver').'/icon-profile.png';
            }
            //avatar border
            $cmdBd = "convert -border 6x6 $avatarPath $avatarPathBd";
            exec($cmdBd);

        }
        /*if($size == "1920x1080")
        {
            $thumbPath = public_path("/montage_video/thumb_1920_1080.jpg");
        }
        else
        {
            $thumbPath = public_path("/montage_video/thumb.jpg");
        }*/
        /*$ffmpegBg = "ffmpeg -loop 1 -i ".$thumbPath." -i ".$audio." -shortest -c:v libx264 -c:a copy -vf scale=".$size." ".$videoBackground;*/
        $ffmpegBg = "ffmpeg -loop 1 -i ".$thumbPath." -i ".$audio." -c:v libx264 -vf scale=".$size." -t 3.0 ".$videoBackground;
        exec($ffmpegBg);
        if(file_exists($videoBackground)) {
            $videoIntro = $path."videoIntro.mp4";
            if($size == "1920x1080")
            {
                $ffmpegIntro = 'ffmpeg -i '.$videoBackground.' -i '.$avatarPathBd.' -filter_complex "[1:v]scale=340:340 [ovrl], [0:v][ovrl]overlay=(if(eq(gt(t\,2)\,0)\,(t*main_w/7-200)\,(main_w-overlay_w)/2-430)):(main_h-overlay_h)/2, drawtext=fontfile='.$fontHel.': text=\''.$textIntro["username"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-210)): y=h/2-120: fontsize=100:fontcolor=#ffffff,drawtext=fontfile='.$fontHel.': text=\''.$textIntro["top"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-210)): y=h/2-5: fontsize=60:fontcolor=#ffffff,drawtext=fontfile='.$fontHel.': text=\''.$textIntro["date"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-210)): y=h/2+80: fontsize=60:fontcolor=#ffffff" '.$videoIntro;
            }
            else
            {
                $ffmpegIntro = 'ffmpeg -i '.$videoBackground.' -i '.$avatarPathBd.' -filter_complex "[1:v]scale=220:220 [ovrl], [0:v][ovrl]overlay=(if(eq(gt(t\,2)\,0)\,(t*main_w/7.7-110)\,(main_w-overlay_w)/2-300)):(main_h-overlay_h)/2, drawtext=fontfile='.$fontHel.': text=\''.$textIntro["username"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-160)): y=h/2-90: fontsize=70:fontcolor=#ffffff,drawtext=fontfile='.$fontHel.': text=\''.$textIntro["top"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-160)): y=h/2-5: fontsize=40:fontcolor=#ffffff,drawtext=fontfile='.$fontHel.': text=\''.$textIntro["date"].'\': x=(if(eq(gt(t\,2)\,0)\,(w/(1.3*t))\,w/2-160)): y=h/2+50: fontsize=40:fontcolor=#ffffff" '.$videoIntro;
            }

            
            exec($ffmpegIntro);
            if(file_exists($videoIntro)) {
                //unlink($thumbPath);
                if (file_exists($videoBackground)){
                    unlink($videoBackground);
                }
                $file = fopen($path."list.txt","a");
                fwrite($file, $videoIntro . "\n");
                fclose($file);
            }
        }
    }
    public static function concatVideos($userId)
    {
        $montage = storage_path('montage');
        $path = $montage."/".$userId."/";
        $pathAddText = $path . "list.txt";
        $pathMontage = $path . "montage.mp4";
        /*$ffmpeg = "ffmpeg -f concat -safe 0 -i ".$pathAddText." -c copy " . $pathMontage;*/
        $ffmpeg = FFmpegHelper::generateConcatCmd($pathAddText, $pathMontage);
        exec($ffmpeg);
        if(file_exists($pathMontage)) {
            return $pathMontage;
        }
        else return false;
    }
    public static function reEncode($path, $videoPath, $timeCut = 2)
    {
        $filename = pathinfo($videoPath)["filename"];
        $ext = pathinfo($videoPath)["extension"];
        $out = $path.$filename."_out.".$ext;
        $reEncode = $path.$filename."_re.".$ext;
        $videoAu = $path.$filename."_au.".$ext;
        $ffprobe = "ffprobe -i ".$videoPath." -v quiet -print_format json -show_streams -select_streams a -loglevel error";
        $outputjson = shell_exec($ffprobe);
        $outArr = json_decode($outputjson, true);
        if(!isset($outArr["streams"][0]["sample_rate"]))
        {
            Log::info("Video has not audio");
            $ffmpegAddAudio = "ffmpeg -f lavfi -i anullsrc=channel_layout=stereo:sample_rate=48000 -i ".$videoPath." -shortest -c:v copy -c:a aac -strict -2 " . $videoAu;
            exec($ffmpegAddAudio);
            if(file_exists($videoAu)) {
                unlink($videoPath);
                $videoPath = $videoAu;
            }
        }
        
        $ffmpegCut = "ffmpeg -ss ".$timeCut." -i ".$videoPath." -c copy ".$out;
        exec($ffmpegCut);
        if(file_exists($out)) {
            $ffmpeg = "ffmpeg -i ".$out." -qscale 0 -profile main -preset veryfast -y ".$reEncode;
            exec($ffmpeg);
            if(file_exists($reEncode)) {
               unlink($videoPath);
               unlink($out);
                return $reEncode;
            }
        }
        else return false;
    }
    public static function uploadMontage($filePath, $info)
    {
        try
        {
        $montage = storage_path('montage');
        $file = fopen($filePath, 'r');
        $filename = pathinfo($filePath)["basename"];
        $env = config("video.createMontageFor");
        $url = url('http://localhost/afkvr/server/public/api/uploadvideo');
        if ($env == "real") {
            $url = url('https://boom.tv/api/uploadvideo');
        }
        elseif ($env == "beta")
        {
            $url = url('https://beta.boom.tv/api/uploadvideo');
            //beta
        /*$info["token"] = "eyJpdiI6ImRPaGF3T0dQcVJ3YlB4XC9FVTBjVzVnPT0iLCJ2YWx1ZSI6IjFOUVo5bnZmdmZtSFRBQ2MwK3A5aDhkREdUTzZ1ZXZXSEJOdktWT01iQTg9IiwibWFjIjoiMDQwOWNmNDhjNjA5NWY1OGZiOTJiNjQ5NzVmMzY4MGVmYTk0MWMyNGYyNzE3ZDFiYzRhNTliZjE0MzIxNThkMiJ9";*/
        }
        else
        {
            $info["token"] = "eyJpdiI6Ikl1cytrN0pINUtwSlJcL2NxaXd0NDdnPT0iLCJ2YWx1ZSI6Ild2ZFFQNFNtY0l2TGNTMTA5RzRCMFdJcXNpZU9GZVl5UzVVckFpYWZwNlU9IiwibWFjIjoiYTMwNmQzMzJkMmVkMDExZGQ5YTY1NWI4MjEwZGQyMmNmY2FjMDA0MjJjZmUxZGViZTA0ZTU4NmY4Zjc2OTE1ZSJ9";
        }
        
        $client = new Client([
                    'timeout'  => 300,
                ]);
        $res = $client->post(
                    $url,
                    array(
                        'multipart' => [
                            [
                                'name'     => "file",
                                'contents' => $file,
                            ],
                            [
                                'name'     => 'type',
                                'contents' => Video::TYPE_MONTAGE,
                            ],
                            [
                                'name'     => 'game',
                                'contents' => $info["listGame"],
                            ],
                            [
                                'name'     => 'title',
                                'contents' => 'Montage',
                            ],
                            [
                                'name'     => 'token',
                                'contents' => $info["token"],
                            ],
                            [
                                'name'     => 'likes',
                                'contents' => $info["maxLikes"],
                            ],
                            [
                                'name'     => 'views',
                                'contents' => $info["maxViews"],
                            ],
                            [
                                'name'     => 'datetime',
                                'contents' => time(),
                            ],
                            [
                                'name'     => 'create_montage',
                                'contents' => 1,
                            ],
                            [
                                'name'     => 'session_id',
                                'contents' => $info["session_id"],
                            ]
                        ],
                    )
                );
        $response =  json_decode($res->getBody());
        Log::info("response upload file: " . $res->getBody());
        $response =  json_decode($res->getBody());
        if($response->status == 0)
        {
            $path = $montage . "/" . $info["userId"]."/";
            //exec("cd " . $path . " && " . "rm '!(avatar.png)'"); 
            exec("cd " . $path . " && " . "rm *"); 
            return true;
        }
        } catch (\Exception $e) {
             Log::info("Upload video montage error: " . $e->getMessage());
        }
        return false;
    }
}