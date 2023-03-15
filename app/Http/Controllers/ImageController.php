<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App;
use App\Models\User;
use App\Models\Image;
use App\Models\ImageChannel;
use Log;
use App\Helpers\Helper;
use App\Helpers\AWSHelper;
use DB;
class ImageController extends Controller
{
	public function like(Request $request)
	{
        Log::info("like image");
		$icode = $request->input('icode');
        $image_info = Image::where('code', '=', $icode)->first();
        if ($image_info != null) {
            $likes = $image_info->like_numb;
            $like_state = $request->session()->get('img_like_state.' . $icode);
            if ($like_state['like_state'] == true && $like_state['image_code'] == $icode) {
                if ($likes > 0) $likes--;
                $like_state = [
                    "image_code" => $icode,
                    "like_state" => false
                ];
            } else {
                $likes++;
                $like_state = [
                    "image_code" => $icode,
                    "like_state" => true
                ];
            }
            $request->session()->put('img_like_state.' . $icode, $like_state);
            Image::where('code', '=', $icode)->update(["like_numb" => $likes]);
            return response()->json(['like' => $likes, 'like_state' => $like_state['like_state']]);
        } else
            return response()->json(['like' => 0, 'like_state' => 0]);
	}
	public function index(Request $request)
	{
		$streamer = $request->input('streamer');
		$offset = 0;
		$limit = 10;
		$icode = $request->input('i');
		$isDetail = false;
		$image = Image::with("user")->with("imageChannel")->where("code", $icode)->first();
		if($image != null)
		{
			$image->view_numb = $image->view_numb + 1;
			$image->save();
			$isDetail = true;;
		}
		$channels = ImageChannel::all();
		return  view('image.index', 
		["channels" => $channels, "isDetail" => $isDetail,
		"sourceLink" => config("aws.sourceLink"), "image" => $image]);
	}

	public function filterImage(Request $request)
	{
		$filterBy = $request->input("filterBy");
		$channelId = $request->input("channelId");
        $streamer = $request->input("streamer");
        $page = $request->input("page");
        $limit = $request->input("limit");
        $page = isset($page) ? $page : 1;
        $page = ($page >= 1) ? $page : 1;
        $limit = isset($limit) ? $limit : 10;
        $offset = ($page - 1) * $limit;
        $images = Image::getImgByCondition($filterBy, $limit, $offset, $streamer, $channelId);
        $filterView = "images";
        $content = view('image.' . $filterView, ["images" => $images, 
            "sourceLink" => config("aws.sourceLink")])->render();
        return response()->json(['content' => $content, "channelId" => $channelId]);
	}
}
