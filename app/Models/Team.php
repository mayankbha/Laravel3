<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use App\Helpers\AWSHelper;

class Team extends Model
{
    protected $table = 'teams';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'name', 'owner_id', 'banner_link', 'created_at', 'updated_at'];

    public static function uploadBanner($file, $teamId)
    {
    	$extension = $file->getClientOriginalExtension();
        if(substr($file->getMimeType(), 0, 5) == 'image')
        {
            $realPath = $file->getRealPath();
            $storage = storage_path('team_banner');
            if (!is_dir($storage)) {
                mkdir($storage);
            }
            $bannerPath = $storage . "/" . $teamId;
            if (!is_dir($bannerPath)) {
                mkdir($bannerPath);
            }
            $bucket = config("aws.bucket_contents");
            $folderServer = strtolower(config("aws.folder_client"));
            $folder = config("aws.folder_team_banner")."/".$folderServer."/".$teamId."/";
            $fileName = $file->getClientOriginalName();
			$realPath = $file->getRealPath();
            $info = AWSHelper::uploadToBucket($realPath, $folder, $fileName, $bucket, "public-read");
            exec("rm -rf " . $bannerPath . "/*");
            Team::find($teamId)->update(["banner_link" => "/".$folder . $fileName]);
        }
        return false;
    }

    public static function removeUser($users, $teamId)
    {
    	$ids = User::whereIn("name", $users)
    			->where("type", User::USER_TYPE_TWITCH)
    			->where("team_id", $teamId) 
    			->pluck("id");
    	$ownerTeam = Team::whereIn("owner_id", $ids)->update(["owner_id"=> 0]);	
    	$user = User::whereIn("name", $users)
    			->where("type", User::USER_TYPE_TWITCH)
    			->where("team_id", $teamId) 
    			->update(["team_id" => 0]);
    }

    public static function updateTeamForUser($users, $teamId)
    {
    	$user = User::where("type", User::USER_TYPE_TWITCH)
    			->where("team_id", $teamId) 
    			->update(["team_id" => 0]);
    	$user = User::whereIn("name", $users)
    			->where("type", User::USER_TYPE_TWITCH) 
    			->update(["team_id" => $teamId]);
    }

    public static function deleteTeam($teamId)
    {
    	User::where("team_id", $teamId)->update(["team_id" => 0]); 
    	Team::find($teamId)->delete();
    }
}