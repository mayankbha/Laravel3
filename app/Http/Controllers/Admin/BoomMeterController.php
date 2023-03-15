<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;
use App\Models\User;
use App\Models\BoomMeter;
use App\Models\BoomMeterType;
use App\Helpers\AWSHelper;
use App\Helpers\Helper;
use Redirect;
use Log;

class BoomMeterController extends Controller
{
    public function getUploadImageBoomMeter(Request $request)
    {
        $username = $request->input("search");
        if(isset($username) && $username != "")
        {
            $users = User::where("name", $username)->paginate(50);
        }
        else
        {
            $users = User::orderby("created_at","desc")->paginate(50);
        }
        
        return view('admin.boom_meter.index', ["users" => $users]);
    }

    public function review(Request $request, $code){
        $return  = BoomMeter::getInfoToReview($code, true);
        return view('admin.boom_meter.review', $return);
    }

    public function customBoomMeter(Request $request, $code){
        $user = User::where("code", $code)->first();
        $links3 = config("aws.linkS3BoomMeter")."default/";
        $images = ["1.gif", "2.gif", "3.gif", "4.gif", "5.gif" , "6.gif", "7.gif", "8.gif", "9.gif", "CD5half.gif", "CDRising.gif", "CDStatic.png"];
        return view('admin.boom_meter.custom', ["user" => $user, "code" => $code, "images" => $images, "links3" => $links3]);
    }

    public function uploadCss(Request $request)
    {
        $code = $request->input("code");
        $active = $request->input("active");
        if($active == "true") $active = 1;
        else $active = 0;
        $content = $request->input("content");
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
            return response()->json(['status' => 1, 'message' => 'Write file error!']);
        }

        AWSHelper::uploadToBucket($source, $folder."/".$code."/", "style.css", $bucket, "public-read");
        exec("rm -rf " . $boomPath . "/*");
        // save db
        $boomMeter = BoomMeter::where("user_code", $code)->first();
        if($boomMeter == null) $boomMeter = new BoomMeter();
        $boomMeter->user_code = $code;
        $boomMeter->status = $active;
        $boomMeter->custom_style = 1;
        $boomMeter->save();
        return response()->json(['status' => 0, 'message' => 'Set css successly!']);
    }
    public function uploadImageBoomMeter(Request $request)
    {
        $file = $request->file("file");
        $code = $request->input("code");
        if ($request->hasFile('file'))
        {
            $result = BoomMeter::uploadBoomMeter($file, $code);
            if(!$result)
            {
                return Redirect::back()->withErrors(['File type is zip']);
            }
        } 
        return Redirect::route('admin.reviewBoomMeter', ["code" => $code]);          
    }
}
