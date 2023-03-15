<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BoomMeterHistory;
use File;
use Log;
use App\Helpers\AWSHelper;
use URL;

class BoomMeter extends Model
{
    protected $table = 'boom_meter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_code', 'status', 'created_at', 'updated_at', 'custom_img', 'custom_style', 'timestamp', 'boom_meter_type_id', 'has_zero'];

    const ACTIVE_STATUS = 1;

    public static function installBoomMeter($usercode, $boomMeterTypeId)
    {
        $boomMeter = BoomMeter::where("user_code", $usercode)->first();

        if($boomMeter == null) $boomMeter = new BoomMeter();
        $boomMeter->boom_meter_type_id = $boomMeterTypeId;
        $boomMeter->timestamp = time();
        $boomMeter->save();
    }
    public static function uploadBoomMeter($file, $code)
    {
        $extension = $file->getClientOriginalExtension();
        if($extension == "zip")
        {
            $realPath = $file->getRealPath();
            $storage = storage_path('boom_meter');
            if (!is_dir($storage)) {
                mkdir($storage);
            }
            $boomPath = $storage . "/" . $code;
            if (!is_dir($boomPath)) {
                mkdir($boomPath);
            }
            $filezip = $file->getClientOriginalName();
            $actualName = pathinfo($filezip,PATHINFO_FILENAME);
            $file->move($boomPath, $filezip);
            $path = $boomPath . "/" . $actualName;
            exec("cd '".$boomPath."' && unzip '" . $boomPath."/".$filezip."' -d '".$path."'", $result, $returnval);
            $bucket = config("aws.bucket_contents");
            $folderServer = strtolower(config("aws.folder_client"));
            $folder = config("aws.folder_boom_meter")."/".$folderServer;
            $files = File::allFiles($path);
            $hasZero = 0;
            foreach ($files as $key => $f) {
                AWSHelper::uploadToBucket($f->getPath()."/".$f->getFilename(), $folder."/".$code."/", $f->getFilename(), $bucket, "public-read");
                if($f->getFilename() == "0.gif") $hasZero = 1;
            }
            exec("rm -rf " . $boomPath . "/*");
            $boomMeter = BoomMeter::where("user_code", $code)->first();
            if($boomMeter == null) $boomMeter = new BoomMeter();
            $boomMeter->user_code = $code;
            $boomMeter->custom_img = 1;
            $boomMeter->custom_style = 0;
            $boomMeter->has_zero = $hasZero;
            $boomMeter->timestamp = time();
            $boomMeter->boom_meter_type_id = BoomMeterType::where("type",BoomMeterType::CUSTOM_TYPE)->first()->id;
            $boomMeter->save();
            User::where("code", $code)->update(["allow_custom_meter" => 1]);
            return true;
        }
        return false;
    }

    public static function uploadCssToS3($code, $content)
    {
        $bucket = config("aws.bucket_contents");
        $storage = storage_path('boom_meter');
        if (!is_dir($storage)) {
            mkdir($storage);
        }
        $boomPath = $storage . "/" . $code;
        if (!is_dir($boomPath)) {
            mkdir($boomPath);
        }
        $source = $boomPath . "/style.css";
        $folderServer = strtolower(config("aws.folder_client"));
        $folder = config("aws.folder_boom_meter")."/".$folderServer;
        $bytes_written = File::put($source, $content);
        if ($bytes_written === false)
        {
            return false;
        }

        AWSHelper::uploadToBucket($source, $folder."/".$code."/", "style.css", $bucket, "public-read");
        exec("rm -rf " . $boomPath . "/*");
        // save db
        $boomMeter = BoomMeter::where("user_code", $code)->first();
        if($boomMeter == null) $boomMeter = new BoomMeter();
        $boomMeter->user_code = $code;
        $boomMeter->custom_style = 1;
        $boomMeter->save();
        return true;
    }

    public static function getInfoToReview($token, $isDemoCustom = false)
    {
        $twitch = SocialAccount::where("access_token", $token)->first();
        $userByCode = User::where("code", $token)->first();
        $chanel = "";
        $folderServer = strtolower(config("aws.folder_client"));
        $links3 = config("aws.linkS3BoomMeter");
        $path = URL::to('/') . "/boom_status/image_gif/";
        $pathDefault = $links3 . "default/";
        $cssLink = $pathDefault . "style.css";
        $checkDefault = -1;
        $version = "?0";
        $urlSocket = config('socket.url');
        if ($twitch != null) {
            $userByCode = User::find($twitch->user_id);
            $chanel = $userByCode->name;
        }
        $boomMeterTypeId = null;
        $userCode = "";
        $hasZero = 0;
        if ($userByCode != null) {
            if($userByCode->type == User::USER_TYPE_MIXER)
            {
                $urlSocket = config('socket.url_mixer');
            }
            if ($userByCode->type == User::USER_TYPE_YOUTUBE) {
                $urlSocket = config('socket.url_youtube');
            }
            $userCode = $userByCode->code;
            $chanel = $userByCode->name;
            $boomMeter = BoomMeter::where("user_code", $userByCode->code)
                ->first();
            if ($boomMeter != null) {
                $hasZero = $boomMeter->has_zero;
                $links3 = config("aws.linkS3BoomMeter") . $folderServer . "/";
                $cssLink = $links3 . "defaults/style.css";
                $typeMeter = BoomMeterType::find($boomMeter->boom_meter_type_id);
                $boomMeterTypeId = $boomMeter->boom_meter_type_id;
                $version = "?".$typeMeter->version;
                if (($typeMeter->type != BoomMeterType::CUSTOM_TYPE && 
                    $isDemoCustom == false)
                    || ($typeMeter->type != BoomMeterType::CUSTOM_TYPE && 
                    $isDemoCustom == true && $boomMeter->custom_img != 1))
                {
                    $path = $links3 . $typeMeter->folders3 . "/";
                    /*if ($boomMeter->custom_style) {
                        $cssLink = $links3 . $boomMeter->user_code . "/" . "style.css";
                    }*/
                    $checkDefault = 0;
                    $hasZero = 0;
                } else {
                    $checkDefault = 1;
                    $version = "?" . $boomMeter->timestamp;

                    if ($boomMeter->custom_img) {
                        $path = $links3 . $boomMeter->user_code . "/";
                    }
                    if ($boomMeter->custom_style) {
                        $cssLink = $links3 . $boomMeter->user_code . "/" . "style.css";
                    }
                }
            }
        }
        
        // an ugly hack, with boom_meter_type_id > 6 we will not show the edge around meters
        $useEdge = 1;
        if ($boomMeter != null && $boomMeter->boom_meter_type_id > 6) {
            $useEdge = 0;
        }
        if ($boomMeter == null) {
            $useEdge = 0;
        }
        
        $cssContent = file_get_contents($cssLink.$version);
        $array_from_to = ["#" => ".",
                          "}" => "z-index: 999; }"];
        $cssContentForZero = strtr($cssContent, $array_from_to);
        return ["chanel" => $chanel, "path" => $path,
            "cssLink" => $cssLink, "checkDefault" => $checkDefault,
            "cssContent" => $cssContent,
            "version" => $version,
            "boomMeterTypeId" => $boomMeterTypeId,
            "userCode" => $userCode,
            "code" => $userCode,
            "hasZero" => $hasZero,
            "cssContentForZero" => $cssContentForZero,
            "urlSocket" => $urlSocket,
            "useEdge" => $useEdge];
    }
}