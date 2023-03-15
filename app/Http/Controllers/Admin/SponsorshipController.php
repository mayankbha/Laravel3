<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;
use App\Models\User;
use App\Helpers\AWSHelper;
use App\Helpers\Helper;
use Redirect;
use Log;
use App\Models\Sponsorship;
use Carbon\Carbon;
use DateTimeZone;

class SponsorshipController extends Controller
{
    public function setSponsorship(Request $request)
    {
        $code = $request->input("code");
        $user = User::where("code", $code)->first();
        $info = array();
        $sponsorshipOld = Sponsorship::where("user_id", $user->id)->orderby("created_at", "desc")->get();
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        foreach ($sponsorshipOld as $sp) {
            $spon = array();
            $spon["status"] = $sp->getStatus(); 
            $spon["id"] = $sp->id; 
            $spon["status_name"] = "Active";
            if($spon["status"] == Sponsorship::FUTURE_STATUS)
            {
                $spon["status_name"] = "Coming";
            }
            if($spon["status"] == Sponsorship::EXPIRE_STATUS)
            {
                $spon["status_name"] = "Expired";
            }
            if($spon["status"] == Sponsorship::DELETED_STATUS)
            {
                $spon["status_name"] = "Deleted";
            }
            $spon["link"] = $sp->video_link; 
            $spon["timezone"] = $sp->timezone;
            // convert from utc to timezone
            $spon["starttime"] = Helper::convertTimeZone($sp->starttime, "Y-m-d H:i:s", "m/d/Y H:i A", "UTC", $sp->timezone);
            $spon["stoptime"] = Helper::convertTimeZone($sp->expiredtime, "Y-m-d H:i:s", "m/d/Y H:i A", "UTC", $sp->timezone);
            $info[] = $spon;
        }
        /*if($sponsorshipOld != null)
        {
            $info["link"] = $sponsorshipOld->video_link; 
            $info["timezone"] = $sponsorshipOld->timezone;
            // convert from utc to timezone
            $info["starttime"] = Helper::convertTimeZone($sponsorshipOld->starttime, "Y-m-d H:i:s", "m/d/Y H:i A", "UTC", $sponsorshipOld->timezone);
            $info["stoptime"] = Helper::convertTimeZone($sponsorshipOld->expiredtime, "Y-m-d H:i:s", "m/d/Y H:i A", "UTC", $sponsorshipOld->timezone);
        }*/
        
        return view('admin.sponsorship.index', ["user" => $user, "info" => $info, "timezones" => $timezones]);
    }
    public function uploadSponsorship(Request $request)
    {
    	$starttime = $request->input('starttime');
        $expiretime = $request->input('expiretime');
        $userId = $request->input('user_id');
        $code = $request->input('user_code');
        $timezone = $request->input('timezone');
    	if ($request->hasFile('file')) 
        {
		    $file = $request->file("file");
		    if ($file->isValid()) 
            {
                $folderServer = strtolower(config("aws.folder_client"));
                $folder = config("aws.folder_sponsorship_video")."/".$folderServer."/".$code."/";
                $realPath = $file->getRealPath();
                $path = storage_path('upload_sponsorship_video');
                if (!is_dir($path)) {
                    mkdir($path);
                }
                $destinationPath = storage_path('upload_sponsorship_video/' . $code."/");
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath);
                }
                $videoName = preg_replace("/[\'^£$%&*()}{@#~?><>,\/|=_+¬\s+]/u ",'_',$file->getClientOriginalName());
                $file->move($destinationPath, $videoName);
                $duration = ceil(Helper::getDurationLength($destinationPath."/".$videoName));
                
                $start = Helper::convertTimeZone($starttime, "m/d/Y H:i A", "Y-m-d H:i:s", $timezone, "UTC");
                $expire = Helper::convertTimeZone($expiretime, "m/d/Y H:i A", "Y-m-d H:i:s", $timezone, "UTC");
                Log::info($starttime . "===" . $expiretime);
                Log::info($start . "===" . $expire);
                $bucket = config("aws.bucket_contents");
                $date = time();
                $actual_name = pathinfo($videoName,PATHINFO_FILENAME);
                $extension = pathinfo($videoName, PATHINFO_EXTENSION);
                $actual_name = $actual_name.'_'.$date;
                $s3Name = $actual_name.".".$extension;
                AWSHelper::uploadToBucket($destinationPath."/".$videoName, $folder, $s3Name, $bucket, "public-read");
                $link = config("aws.cloudfront_content")."/".$folder.$s3Name;
                $sponsorship = new Sponsorship();
                $sponsorship->user_id = $userId;
                // save time with UTC timezone
                $sponsorship->starttime = $start;
                $sponsorship->expiredtime = $expire;
                $sponsorship->duration = $duration;
                $sponsorship->video_link = $link;
                $sponsorship->timezone = $timezone;
                $sponsorship->save();
		    }
		}
        return redirect()->to(route('admin.setSponsorship', ["code" => $code]));
    }
    public function deleteSponsorship(Request $request)
    {
        $id = $request->input('spon_id');
        $code = $request->input('code');
        $spon = Sponsorship::find($id);
        $user = User::where("code", $code)->first();
        if($user != null)
        {
            $spon->deleteSponsorship();
        }
        return redirect()->to(route('admin.setSponsorship', ["code" => $code]));
    }

}