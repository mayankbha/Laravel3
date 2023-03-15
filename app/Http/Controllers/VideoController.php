<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;
use App\Models\Video;
use App\Models\Rating;
use App\Models\Like;
use App\Models\Game;
use App\Models\SocialAccount;

Use App\Jobs\RemoveVideoOnS3;

use Auth;
use Lang;

class VideoController extends Controller
{
    //
    /**
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove_video(Video $video)
    {

        if ($video->user_id != Auth::id()) {
            abort(403);
        }
        $video->status = 3;
        $return = $video->save();

        if ($return) {
            return response()->json(['state' => $return, 'msg' => Lang::get('video.success_remove_video'), 'video' => $video->toJson()]);
        } else {
            return response()->json(['state' => $return, 'msg' => Lang::get('video.error_remove_video'), 'video' => $video->toJson()]);
        }

    }

    public function my_video(Request $request)
    {
        $user_id = $request->input("uid");
        $filterBy = $request->input("filterBy");
        $gameId = $request->input("gameId");
        $page = $request->input("page");
        $limit = $request->input("limit");
        $teamId = $request->input("teamId");
        $container = $request->input("container");
        $json = $request->input('json');
        $page = isset($page) ? $page : 1;
        $page = ($page >= 1) ? $page : 1;
        $limit = isset($limit) ? $limit : 12;
        $offset = ($page - 1) * $limit;
        $videos = Video::filter_video_by_userid($filterBy,$user_id, $gameId, $offset, $limit);
        $imageDefault = config('content.cloudfront').'/assets/'.config('content.assets_ver').'/image-default.png';
        $filterView = "video";
        if ($filterBy == Video::FILTER_CAROUSEL) $filterView = "carousels";
        $content = view('home.' . $filterView, ["videos" => $videos,"container"=>$container,"imageDefault"=>$imageDefault])->render();
        if ($json){
            $data_json = [];
            foreach ($videos as $item){
                $user = $item->user()->first();
                $temp_item['code'] = $item->code;
                $temp_item['link'] = route('playvideo').'?v='.$item->code;
                $temp_item['type'] = $item->type;
                $temp_item['thumbnail'] = config('aws.sourceLink').$item->thumbnail;
                $temp_item['default_image'] = $imageDefault;
                $temp_item['user_avatar'] = ($user->avatar != null) ? $user->avatar : config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png';
                $temp_item['user_displayname'] = $user->displayname;
                $temp_item['user_profile'] = route('profile',[$user->name]);
                $temp_item['game_name'] = $item->getGameNames();
                $temp_item['game_name_display'] = str_limit($temp_item['game_name']);
                $temp_item['like_numb'] = $item->like_numb;
                $temp_item['view_numb'] = $item->getViewNumbSort();
                $temp_item['auth'] = ($user->id == auth()->id());
                $temp_item['is_leader_team'] = false;
                if($teamId == auth()->id())
                {
                    $temp_item['is_leader_team'] = true;
                }
                $temp_item['links3'] = config("aws.cloudfront").$item->links3;
                $temp_item['id'] = $item->id;
                $temp_item['container'] = $container;
                $temp_item['date_time_zone'] = $item->formatTime();
                $temp_item['hour_time_zone'] = $item->formatTime(0);
                $data_json[] = $temp_item;
            }
            if (isset($gameId)) {
                return response()->json(['content' => $data_json, 'container' => $container ,'gameId' => $gameId, 'count' => $videos->count()]);
            } else {
                return response()->json(['content' => $data_json, 'container' => $container, 'count' => $videos->count(), 'userId' => $user_id]);
            }
        }
        else{
            if (isset($gameId)) {
                return response()->json(['content' => $content, 'container' => $container ,'gameId' => $gameId, 'count' => $videos->count()]);
            } else {
                return response()->json(['content' => $content, 'container' => $container, 'count' => $videos->count()]);
            }
        }
    }
}
