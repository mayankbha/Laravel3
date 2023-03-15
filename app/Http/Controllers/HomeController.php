<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App;
use TwitchApi;
use Auth;
use App\Models\User;
use App\Models\Video;
use App\Models\Rating;
use DB;
use App\Models\Like;
use App\Models\Game;
use Log;
use App\Models\Contact;
use App\Helpers\Helper;
use App\Helpers\AWSHelper;
use Mail;
use App\Models\SocialAccount;
use App\Models\VideoGame;
use App\Models\Subscription;
use View;
use App\Models\ViewDay;
use App\Http\Controllers\Auth\AuthController;
use Lang;
use App\Models\BoomMeter;
use App\Models\SessionStreamer;
use URL;
use Cache;
use App\Models\VrbetaUser;
class HomeController extends Controller
{
    public function embedVideo($code)
    {
        if ($code != "") {
            $video = Video::where('code', $code)->first();
            if ($video) {
                $sourceLink = config("aws.cloudfront");
                $link = $sourceLink . $video->links3;
                $poster = $sourceLink . $video->thumbnail;
                $link360 = "";
                $linkHls = "";
                if ($video->type == 2)
                    $link360 = [
                        '2048' => str_replace($sourceLink, $sourceLink . "/videos-360/2048", $link),
                        '1440' => str_replace($sourceLink, $sourceLink . "/videos-360/1440", $link),
                        '1080' => str_replace($sourceLink, $sourceLink . "/videos-360/1080", $link),
                        '720' => str_replace($sourceLink, $sourceLink . "/videos-360/720", $link),
                    ];
                if ($video->hls_type == 3)
                    $linkHls = $sourceLink . $video->link_hls;

                return view('embed.embed_player',
                    [
                        "video" => $video,
                        "link" => $link,
                        "poster" => $poster,
                        "linkHls" => $linkHls,
                        "link360" => $link360
                    ]);
            } else
                return view('errors.video_notfound');
        }
        return view('errors.errors');
    }

    public function setUserZone(Request $request)
    {
        $user_zone = $request->input('user_zone');
        if ($user_zone != "") {
            $cookie = cookie('user_zone', $user_zone, 216000);

            if ($request->cookie('user_zone')) {

                return response(1)->cookie($cookie);
            } else {

                return response(0)->cookie($cookie);
            }

        }


    }

    public function getShare(Request $request)
    {
        $vcode = $request->input('vcode');
        $video_info = Video::where('code', '=', $vcode)->first();
        if ($video_info != null) {
            $result = file_get_contents("http://graph.facebook.com/?id=" . $video_info->link);
            $share = 0;

            try {
                $share = json_decode($result)->share->share_count;
                $video_info->update([
                    "share_numb" => $share
                ]);
                return $share;

            } catch (\Exception $e) {
                $video_info->share_numb;
            }

        } else
            return "0";
        return 0;


    }

    public function getLike(Request $request)
    {
        $vcode = $request->input('vcode');
        $video_info = Video::where('code', '=', $vcode)->first();

        if (!$video_info) {
            return 0;
        }
        return $video_info->like_numb;


    }

    //inc like
    public function likeVideo(Request $request)
    {
        $vcode = $request->input('vcode');
        $video_info = Video::where('code', '=', $vcode)->first();
        if ($video_info != null) {
            $likes = $video_info->like_numb;

            $like_state = $request->session()->get('like_state.' . $vcode);
            if ($like_state['like_state'] == true && $like_state['video_code'] == $vcode) {
                if ($likes > 0) $likes--;
                $like_state = [
                    "video_code" => $vcode,
                    "like_state" => false
                ];
            } else {
                $likes++;
                $like_state = [
                    "video_code" => $vcode,
                    "like_state" => true
                ];
            }
            //$request->session()->forget('like_state');
            $request->session()->put('like_state.' . $vcode, $like_state);
            $video_info->like_numb = $likes;
            $video_info->save();

            // check like by super user
            if (Auth::check()) {
                $id = Auth::id();
                Like::updateLike($id, $video_info->id);
            }

            return response()->json(['like' => $likes, 'like_state' => $like_state['like_state']]);
        } else
            return response()->json(['like' => 0, 'like_state' => 0]);
    }
    // public function likeVideo(Request $request){
    //  $vcode=$request->input('vcode');
    //  $video_info =Video::where('code','=',$vcode)->first();
    //  if($video_info!=null && Auth::check())
    //     {

    //      $video_id=$video_info->id;
    //      $user_id= Auth::user()->id;
    //      $like=Like::where('video_id',$video_id)->where('user_id',$user_id)->first();
    //      if($like)
    //      {
    //          if($like->like_state==true)
    //              $like->like_state=false;
    //          else
    //              $like->like_state=true;
    //          $like->save();
    //      }
    //      else
    //      {
    //          $like= new Like();
    //          $like->video_id=$video_id;
    //          $like->user_id=$user_id;
    //          $like->like_state=true;
    //          $like->save();
    //      }

    //     }
    //     $like=Like::where('video_id',$video_info->id)->where('like_state',true)->get();
    //     Video::where('code','=',$vcode)->update(['like_numb'=>count($like)]);
    //  return count($like);


    // }
    //end
    //get view
    public function getView(Request $request)
    {
        $vcode = $request->input('vcode');
        $video_info = Video::where('code', '=', $vcode)->first();
        if (!$video_info) {
            return 0;
        }
        return $video_info->view_numb;
    }
    //end get view
    //inc view
    public function incView(Request $request)
    {
        $vcode = $request->input('vcode');
        return Video::updateView($vcode, 1);
    }

    //end inc view

    public function getListVideo(Request $request)
    {
        $offset = $request->input('offset');
        $view = $request->session()->get('view');
        $video_user = $request->session()->get('videoUser');
        $filter_id = $request->session()->get('gameId', 0);
        $is_list_user = false;
        $videos = [];
        $pagination = config("view.page_numb");
        if ($view == "myfeed") {
            if ($filter_id == 0)
                $videos = Video::where('user_id', '=', Auth::user()->id)
                    ->take($pagination)->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
            else
                $videos = Video::where('user_id', '=', Auth::user()->id)
                    ->where('game_id', $filter_id)
                    ->take($pagination)->offset($offset)
                    ->orderBy('created_at', 'desc')->get();

        } else {
            if (!$video_user) {
                if (Auth::check()) {
                    if ($filter_id == 0)
                        $videos = Video::where("user_id", "!=", Auth::id())
                            ->take($pagination)->offset($offset)
                            ->orderBy('created_at', 'desc')
                            ->get();
                    else
                        $videos = Video::where("user_id", "!=", Auth::id())
                            ->where('game_id', $filter_id)
                            ->take($pagination)->offset($offset)
                            ->orderBy('created_at', 'desc')
                            ->get();

                } else {
                    if ($filter_id == 0)
                        $videos = Video::orderBy('created_at', 'desc')
                            ->take($pagination)->offset($offset)->get();
                    else
                        $videos = Video::orderBy('created_at', 'desc')
                            ->where('game_id', $filter_id)
                            ->take($pagination)->offset($offset)->get();
                }
            } else {
                $is_list_user = true;
                if ($filter_id == 0)
                    $videos = Video::where("user_id", "=", $video_user)
                        ->take($pagination)->offset($offset)
                        ->orderBy('created_at', 'desc')
                        ->get();
                else
                    $videos = Video::where("user_id", "=", $video_user)
                        ->where('game_id', $filter_id)
                        ->take($pagination)->offset($offset)
                        ->orderBy('created_at', 'desc')
                        ->get();
            }


        }
        return view('home.list_video', [
            'videos' => $videos,
            "is_list_user" => $is_list_user,
            "view" => $view,
        ]);
    }

    public function playvideo(Request $request)
    {
        $vcode = $request->input('v');
        $view = $request->session()->get('view');
        $video_user = $request->session()->get('videoUser', '');
        $filter_id = $request->session()->get('gameId', 0);
        $sortby = $request->session()->get('sortby', 0);
        $pagination = config('view.page_numb');
        $conditions = ["page" => 1, "game_id" => $filter_id, "sortby" => $sortby, "user_id" => $video_user];

        $video_info = Video::where("status", Video::STATUS_ACTIVE)->where('code', '=', $vcode)->first();


        $videos = [];
        $video_preload = [];
        $author = [];
        $poster = null;
        $is_list_user = false;
        $link = "";

        $vnext = null;
        $vname_next = null;
        $like_state = $request->session()->get('like_state', [
            "video_code" => $vcode,
            "like_state" => false,

        ]);

        if ($like_state['video_code'] == $vcode)
            if ($like_state['like_state'] == true)
                $like_state = "1";
            else
                $like_state = "0";
        else
            $like_state = "0";
        $pagination = config("view.page_numb");
        $title = "";

        $linkHls = "";
        $view_blade = "normal";

        $job_status = true;

        $link360 = [];
        $playHlsByJW = config('video.playHlsByJW');

        $type = 0;
        if ($video_info != null) {
            $type = $video_info->type;
            if ($video_info->job_id != "")
                if ($video_info->job_status != "Complete") {
                    $job = AWSHelper::checkJobStatus($video_info->job_id);
                    if ($job != "Complete")
                        $job_status = false;
                    else
                        Video::where('id', $video_info->id)->update(["job_status" => $job]);

                }

            // if(Auth::check())
            // {
            //  $like= Like::where('user_id',Auth::user()->id)->where('video_id',$video_info->id)->first();
            //  if($like)
            //      $like_state=$like->like_state;

            // }
            $sourceLink = config("aws.sourceLink");
            $link = $sourceLink . $video_info->links3;

            if ($video_info->hls_type == 3) {
                $linkHls = $sourceLink . $video_info->link_hls;
                if ($playHlsByJW) $view_blade = "jwplayer";
            }
            /*if($video_info->type == 2)
            {
                $link360=[
                    '2048'=>str_replace( $sourceLink, $sourceLink."/videos-360/2048", $link),
                    '1440'=>str_replace( $sourceLink, $sourceLink."/videos-360/1440", $link),
                    '1080'=>str_replace( $sourceLink, $sourceLink."/videos-360/1080", $link),
                    '720'=>str_replace( $sourceLink, $sourceLink."/videos-360/720", $link),
                ];
            } */
            if ($video_info->type == 2) {
                $linkHls = url("/hls360/") . $video_info->link_hls;
                $view_blade = "bitmovin";
                $link = str_replace($sourceLink, $sourceLink . "/videos-360/2048", $link);
            }
            $author = User::where('id', '=', $video_info->user_id)->first();
            $title = $video_info->title;
            $poster = $sourceLink . $video_info->thumbnail;
            //view

            $videosData = Video::getVideoByCondition($conditions, $pagination);
            $videos = $videosData["videos"];
            $total = $videosData["total"];

            $count = count($videos);
            if ($count > 0) {
                if ($vcode != $videos[$count - 1]->code)
                    foreach ($videos as $key => $value) {
                        if ($value->code == $vcode) {
                            $vnext = $videos[$key + 1]->code;
                            $vname_next = $videos[$key + 1]->title;
                            break;
                        }
                    }

            }
            return view('player.' . $view_blade, [
                "title" => $title,
                "link" => $link,
                "view" => $view,
                "videos" => $videos,
                "author" => $author,
                "poster" => $poster,
                "is_list_user" => $is_list_user,
                "vcode" => $vcode,
                "vtime" => config('video.vtime'),
                "vnext" => $vnext,
                "vname_next" => $vname_next,
                "ulist" => $video_user,
                "video_info" => $video_info,
                "like_state" => $like_state,
                "linkHls" => $linkHls,
                "job_status" => $job_status,
                "link360" => $link360,
                "playHlsByJW" => $playHlsByJW,
                "total" => $total,
                "request" => $request,
                "type" => $type
            ]);
        } else {
            return view('errors.video_notfound');
        }


    }


    // vote star for video
    public function vote(Request $request)
    {
        $dataRes = array("status" => 0);
        $video = $request->input('video_id');
        $numbStar = $request->input('numb_star');
        $cookieName = 'video_' . $video . '_star';
        $cookie = $request->cookie($cookieName);
        $returnData = Rating::createOrUpdateStar(array(
            "ip" => $request->ip,
            "video_id" => $video,
            "cookie_code" => $cookie,
            "numb_star" => $numbStar
        ));
        $dataRes = array("status" => 1);
        $rateTimeLimit = config("video.rate_time_limit");
        if ($returnData["createNewCookie"]) {
            return response()->json($dataRes)->withCookie($cookieName,
                $returnData["cookie"], $rateTimeLimit);
        } else {
            return response()->json($dataRes);
        }
    }

    public function getVideos(Request $request)
    {
        $view = "";
        $is_list_user = "";
        // get conditions from session
        $videoUser = $request->session()->get('videoUser', '');
        $gameId = $request->session()->get('gameId', '');
        $page = $request->input('offset');
        $sortby = $request->session()->get('sortby', 2);
        // get videos by conditions
        $pagination = config('view.page_numb');
        $conditions = ["page" => $page, "game_id" => $gameId, "sortby" => $sortby, "user_id" => $videoUser];
        $videosData = Video::getVideoByCondition($conditions, $pagination);
        $videos = $videosData["videos"];
        $total = $videosData["total"];
        return view('home.list_video', [
            'videos' => $videos,
            "is_list_user" => $is_list_user,
            "view" => $view,
            "total" => $total,
            "request" => $request
        ]);
    }

    /*public function index($view, Request $request)
    {
        $view = "popular";
        $is_list_user = "";
        // filter by userId
        $videoUser = $request->input('u');
        // $username="";
        // if($videoUser!="")
        // $username=User::find($videoUser)->displayname;
         // filter by gameId
        $gameId = $request->input('gameId');
        $page = 0;
        //0 => "mostRecent", 1 => "oldest" , 2 => "mostFavorited", 3 => "titleVideo"
        $sortby = $request->input('sortby',1);
        // save session
        $request->session()->forget('gameId');
        $request->session()->put('gameId', $gameId);
        $request->session()->forget('sortby');
        $request->session()->put('sortby', $sortby);
        $request->session()->forget('videoUser');
        $request->session()->put('videoUser', $videoUser);
        //get videos by conditions request
        $pagination = config('view.page_numb');
        $conditions = ["page" => $page, "game_id" => $gameId, "sortby" => $sortby, "user_id" => $videoUser];
        $videosData = Video::getVideoByCondition($conditions, $pagination);
        $videos = $videosData["videos"];
        $total = $videosData["total"];
        //get all games
        $games=Game::all();
        $gamename = "All Games";
        //get current game and sort
        $gameFilter = Game::find($gameId);
        if($gameFilter != null) $gamename = $gameFilter->name;
        $sortlist = ["Popularity", "Most Recent", "Oldlest", "Video title", "360 Videos"];
        if(!isset($sortby) || $sortby  == "") $sortby = 0;
        return view('home.index',
            [
                "games"=>$games,
                "videos" => $videos,
                'view' => $view,
                'sortby' => $sortby,
                'video_user' => $videoUser,
                'gameId' => $gameId,
                'gamename' => $gamename,
                'sortlist' => $sortlist,
                'total' => $total,
                'request'=>$request
                // 'username'=>$username
            ]);
    }*/

    public function index(Request $request){
        if (auth()->user()){
            $key_cached = Lang::get('cached.home',['id'=>auth()->id()]);
        }
        else{
            $key_cached = Lang::get('cached.home',['id'=>0]);
        }
        $content = Cache::get($key_cached);

        if ($content != null){
            return $content;
        }
        else{
            $content = $this->indexRender($request);
            Cache::put($key_cached,$content,1);
            return $content;
        }

    }

    public function videoDetail(Request $request){

        $vcode = $request->input('v');
        $ref = $request->input('ref');
        if($ref == "share")
        {
            return $this->videoDetailWithoutList($request);
        }

        if (auth()->user()){
            $key_cached = Lang::get('cached.videoDetail',['id'=>auth()->id(),'vcode'=>$vcode]);
        }
        else{
            $key_cached = Lang::get('cached.videoDetail',['id'=>0,'vcode'=>$vcode]);
        }
        $content = Cache::get($key_cached);

        if ($content != null){
            return $content;
        }
        else{
            $content = $this->videoDetailRender($request);
            Cache::put($key_cached,$content,1);
            return $content;
        }
    }
    public function videoDetailWithoutList(Request $request)
    {
        $vcode = $request->input('v');
        $ref = $request->input('ref');
        if($ref == "share")
        {
            $player = "normal";
            $alertFail = "";
            $isDetail = true;
            $next_video = null;
            $linkHlsLocal = "";
            $message = "";
            $topReplay = "";
            $imageDefault = config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/image-default.png';
             if (isset($vcode) && $vcode != "") {
                $videoDetail = Video::where("code", $vcode)->where('status', 1)->first();
                if (!$videoDetail) {
                    abort(404);
                }
                $isDetail = true;

                if($videoDetail->session_id != null && $videoDetail->session_id != "")
                {
                    $date="";
                    $session = SessionStreamer::find($videoDetail->session_id);
                    $top_montage_numb = config("video.top_montage_numb");
                    if($session != null)
                    {
                        $dateEx = explode(" ", $session->stoptime);
                        $date = strtotime($dateEx[0]);
                        $date = date("m/d", $date);
                    }
                    
                    $topReplay = $videoDetail->user->displayname.": Top replays from ".$date." live stream";
                }
                
                $playHlsByJW = config('video.playHlsByJW');
                if ($videoDetail->hls_type == 3) {
                    $player = "hls";
                    if ($playHlsByJW) $player = "jwplayer";
                    if ($videoDetail->type == 2) {
                        $player = "bitmovin";
                        $linkHlsLocal = url("/hls360/") . $videoDetail->link_hls;
                    }
                }

                $next_video = null;
            }
            else{
                abort(404);
            }
            $streams_video = Video::where('user_id', $videoDetail->user_id)->where('status', 1)->where('id', "<", $videoDetail->id)->orderBy("id", 'desc')->limit(20)->get();
            if(isset($streams_video[0])  && ($videoDetail->type != 2)) {
                $next_streams_video = $streams_video[0]->code;
            } else {
                $streams_video = array();
                $next_streams_video = '';
            }

            return view('home.index',
            [
                "ref" => $ref,
                "videoDetail" => $videoDetail,
                "isDetail" => $isDetail,
                "player" => $player,
                "vcode" => $vcode,
                "vtime" => config('video.vtime'),
                "linkHlsLocal" => $linkHlsLocal,
                "alertFail" => $alertFail,
                "message" => $message,
                "imageDefault" => $imageDefault,
                "next_video" => $next_video,
                "topReplay" => $topReplay,
                "streams_video" => $streams_video,
                "next_streams_video" => $next_streams_video
            ])->render();
        }
    }

    public function videoDetailRender(Request $request)
    {
        $vcode = $request->input('v');
        $autosubscribe = ($request->input('autosubscribe')) ? $request->input('autosubscribe') : "";
        //status claim
        $status = $request->input('status');
        $alertFail = false;
        $message = "";
        if ($status == AuthController::STATUS_ERROR_CLAIM) {
            $alertFail = true;
            $message = "Not match claim user!";
        }
        if ($status == AuthController::STATUS_ERROR_NOT_STREAMER) {
            $alertFail = true;
            $message = "Your twitch is not streamer of BoomApp!";
        }
        $isDetail = false;
        $videoDetail = null;
        $player = "normal";
        $linkHlsLocal = "";
        $next_video = null;
        if (isset($vcode) && $vcode != "") {
            $videoDetail = Video::where("code", $vcode)->where('status', 1)->first();
            if (!$videoDetail) {
                abort(404);
            }
            $isDetail = true;

            $playHlsByJW = config('video.playHlsByJW');
            if ($videoDetail->hls_type == 3) {
                $player = "hls";
                if ($playHlsByJW) $player = "jwplayer";
                if ($videoDetail->type == 2) {
                    $player = "bitmovin";
                    $linkHlsLocal = url("/hls360/") . $videoDetail->link_hls;
                }
            }

            $next_video = Video::where('user_id', $videoDetail->user_id)->where('status', 1)->where('id', "<", $videoDetail->id)->orderBy("id", 'desc')->first();
        }

        $total = config('view.page_index');
        $gameCategory = Game::get_category_game();
        $trending = Video::fiterBy(Video::FILTER_TRENDING);
        $carousels = Video::fiterBy(Video::FILTER_CAROUSEL)->shuffle();
        $imageDefault = config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/image-default.png';
        $streams_video = Video::where('user_id', $videoDetail->user_id)->where('status', 1)->where('id', "<", $videoDetail->id)->orderBy("id", 'desc')->limit(20)->get();
        if(isset($streams_video[0])  && ($videoDetail->type != 2)) {
            $next_streams_video = $streams_video[0]->code;
        } else {
            $streams_video = array();
            $next_streams_video = '';
        }

        $subscribed = "";
        if(Auth::id()){
            $subscription = Subscription::Where(array("streamer_id"=>$videoDetail->user_id, "subscriber_id"=>Auth::id()))->get()->first();
            if(sizeof($subscription)>0 && $subscription->status == Subscription::SUBSCRIBED) {
                $subscribed = 1;
            }
            else if($autosubscribe){
                if(sizeof($subscription)==0){
                    $subscription = new Subscription();
                    $subscription->streamer_id=$videoDetail->user_id;
                    $subscription->subscriber_id=Auth::id();
                    $subscription->status=1;
                    $subscription->save();
                    $subscribed = 1;
                    Cache::flush();
                }
                else if(sizeof($subscription)>0 && $subscription->status == Subscription::UNSUBSCRIBED){
                    $subscription->status=Subscription::SUBSCRIBED;
                    $subscription->save();
                    $subscribed = 1;
                    Cache::flush();
                }
                else {
                    $subscribed = 0;
                }
            }
            else {
                $subscribed = 0;
            }
        }
        
        return view('home.index',
            [
                "carousels" => $carousels,
                "trending" => $trending,
                "page" => 0,
                "total" => $total,
                "listgame" => $gameCategory,
                "videoDetail" => $videoDetail,
                "isDetail" => $isDetail,
                "player" => $player,
                "vcode" => $vcode,
                "vtime" => config('video.vtime'),
                "linkHlsLocal" => $linkHlsLocal,
                "alertFail" => $alertFail,
                "message" => $message,
                "imageDefault" => $imageDefault,
                "next_video" => $next_video,
                "ref" => "",
                "streams_video" => $streams_video,
                "next_streams_video" => $next_streams_video,
                "subscribed" => $subscribed,
            ])->render();
    }

    public function indexRender(Request $request)
    {

        $vcode = $request->input('v');
        //status claim
        $status = $request->input('status');
        $alertFail = false;
        $message = "";
        if ($status == AuthController::STATUS_ERROR_CLAIM) {
            $alertFail = true;
            $message = "Not match claim user!";
        }
        if ($status == AuthController::STATUS_ERROR_NOT_STREAMER) {
            $alertFail = true;
            $message = "Your twitch is not streamer of BoomApp!";
        }
        $isDetail = false;
        $videoDetail = null;
        $player = "normal";
        $linkHlsLocal = "";
        $next_video = null;
        if (isset($vcode) && $vcode != "") {
            $videoDetail = Video::where("code", $vcode)->where('status', 1)->first();
            if (!$videoDetail) {
                abort(404);
            }
            $isDetail = true;

            $playHlsByJW = config('video.playHlsByJW');
            if ($videoDetail->hls_type == 3) {
                $player = "hls";
                if ($playHlsByJW) $player = "jwplayer";
                if ($videoDetail->type == 2) {
                    $player = "bitmovin";
                    $linkHlsLocal = url("/hls360/") . $videoDetail->link_hls;
                }
            }

            $next_video = Video::where('user_id', $videoDetail->user_id)->where('status', 1)->where('id', "<", $videoDetail->id)->orderBy("id", 'desc')->first();
        }
        /*$carousels = Video::fiterBy(Video::FILTER_CAROUSEL);
        $trending = Video::fiterBy(Video::FILTER_TRENDING);
        $highlight = Video::fiterBy(Video::FILTER_HIGHLIGHTS);
        $video360 = Video::fiterBy(Video::FILTER_VIDEO360);
        $videoRecent = Video::fiterBy(Video::FILTER_RECENT);
        $gameCategory = Game::where("is_category", 1)->get();
        $listGame = array();
        foreach ($gameCategory as $g) {
            $games = Video::fiterBy(Video::FILTER_GAME, $g->id);
            $listGame[$g->name] = $games;
        }*/
        $total = config('view.page_index');
        $gameCategory = Game::get_category_game();
        $carousels = Video::fiterBy(Video::FILTER_CAROUSEL)->shuffle();
        $trending = Video::fiterBy(Video::FILTER_TRENDING);
        $imageDefault = config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/image-default.png';
        return view('home.index',
            [
                "carousels" => $carousels,
                "trending" => $trending,
                "page" => 0,
                "total" => $total,
                "listgame" => $gameCategory,
                "videoDetail" => $videoDetail,
                "isDetail" => $isDetail,
                "player" => $player,
                "vcode" => $vcode,
                "vtime" => config('video.vtime'),
                "linkHlsLocal" => $linkHlsLocal,
                "alertFail" => $alertFail,
                "message" => $message,
                "imageDefault" => $imageDefault,
                "next_video" => $next_video,
            ])->render();
    }

    public function filterIndex(Request $request)
    {
        $start_time = microtime(true);
        $filterBy = $request->input("filterBy");
        $gameId = $request->input("gameId");
        $page = $request->input("page");
        $limit = $request->input("limit");
        $container = $request->input("container");
        $json = $request->input('json');
        $last_id = $request->input('last_id');

        $page = isset($page) ? $page : 1;
        $page = ($page >= 1) ? $page : 1;
        $limit = isset($limit) ? $limit : 12;
        $offset = ($page - 1) * $limit;

        $key_cache = Lang::get('cached.filterIndex',['filter'=>$filterBy,'gameid'=>$gameId,'lastid'=>$last_id]);
        $res_content = Cache::get($key_cache);
        if ($res_content != null){
            Log::info("Get filter index cache {$container} {$gameId} in" . (microtime(true) - $start_time));
            return $res_content;
        }

        $videos = Video::fiterBy($filterBy, $gameId, $offset, $limit, $last_id);
        $imageDefault = config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/image-default.png';
        $data_json = [];
        foreach ($videos as $item) {
            $user = $item->user()->first();
            if ($user) {
                $temp_item['code'] = $item->code;
                $temp_item['link'] = route('playvideo') . '?v=' . $item->code;
                $temp_item['type'] = $item->type;
                $temp_item['thumbnail'] = config('aws.sourceLink') . $item->thumbnail;
                $temp_item['default_image'] = $imageDefault;
                $temp_item['user_avatar'] = ($user->avatar != null) ? $user->avatar : config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/icon-1.png';
                $temp_item['user_displayname'] = $user->displayname;
                $temp_item['user_profile'] = route('profile', [$user->name]);
                $temp_item['game_name'] = $item->getGameNames();
                $temp_item['game_name_display'] = str_limit($temp_item['game_name']);
                $temp_item['like_numb'] = $item->like_numb;
                $temp_item['view_numb'] = $item->getViewNumbSort();
                $temp_item['auth'] = ($user->id == auth()->id());
                $temp_item['id'] = $item->id;
                $temp_item['container'] = $container;
                $data_json[] = $temp_item;
            }
        }
        $last_id = ($videos->count()) ? $videos->last()->id : 0;
        if (isset($gameId)) {
            $res_content = response()->json(['content' => $data_json, 'container' => $container, 'gameId' => $gameId, 'count' => $videos->count(), 'last_id' => $last_id]);
        } else {
            $res_content = response()->json(['content' => $data_json, 'container' => $container, 'count' => $videos->count(), 'last_id' => $last_id]);
        }
        Cache::put($key_cache,$res_content,1);
        Log::info("Get filter index {$container} {$gameId} in" . (microtime(true) - $start_time));

        return $res_content;

    }

    public function landing(Request $request)
    {
        $pagination = config('view.page_numb');
        $request->session()->forget('sortby');
        $request->session()->put('sortby', 2); //popular
        $tops = Video::where("featured", ">", 0)->orderBy("featured", "asc")->get();
        $toltal = Video::count();
        return view('home.landing', ["tops" => $tops, "toltal" => $toltal]);
    }

    public function about()
    {
        return view('home.about', []);
    }

    public function comingsoon()
    {
        return view('home.comingsoon', []);
    }

    public function player()

    {
        $link = "http://d2540bljzu9e1.cloudfront.net/csgo360video.mp4";
        $poster = "http://d2540bljzu9e1.cloudfront.net/thumb/Counter-Strike__Global_Offensive_2016-11-10_21-54-06_2016-11-11 05:54:47am.jpg";
        return view('jwplayer.master', ["link" => $link, "poster" => $poster]);
    }

    public function signup(Request $request)
    {
        $email = $request->input('email');
        $twitch_id = $request->input('twitch_id');
        if ($email != "" && $twitch_id != "") {
            $check = Contact::where('email', $email)->first();
            if ($check == null) {
                $item = new Contact();
                $item->email = $email;
                $item->twitch_id = $twitch_id;
                $item->save();
                $code = uniqid() . $item->id;
                $item->update(["code" => $code]);
                Helper::sendMailChimpSubcribe($email, $twitch_id);
                return json_encode(['status' => 0, 'message' => 'Successfully']);
            } else {
                return json_encode(['status' => 1, 'message' => 'Email already exits']);
            }
        } else
            return json_encode(['status' => 1, 'message' => 'Email not be null']);
    }

    public function contact(Request $request)
    {
        $email = $request->input('email');
        $subject = $request->input('subject');
        $content = $request->input('message');
        $data = [
            "email" => $email,
            "subject" => $subject,
            "content" => $content,
            "from" => config("mail.mailFrom.sender")
        ];


        try {
            $mail = config("mail.sendMailContact.sendTo");
            Mail::send('emails.contact', $data, function ($message) use ($mail, $data) {
                $message->from($data['from'], "Contact Email <" . $data['email'] . ">");
                $message->to($mail)->subject("Contact Email <" . $data['email'] . ">");
            });
            return Lang::get('contact.success');
        } catch (\Exception $e) {
            Log::info("Send Mail Error");
            Log::info("Mail Info: " . $email);
            Log::info("Subject: " . $subject);
            Log::info("Content: " . $content);
            Log::error("Error: " . $e);
            return Lang::get('contact.error');
        }

    }

    public function unsubscribe(Request $request)
    {
        $code = $request->input('code');
        $contact = Contact::where('code', $code)->first();
        $message = "This email has already been unsubscribed.";
        if ($contact != null) {
            if ($contact->unsubscribe == 0) {
                $contact->update(["unsubscribe" => 1]);
                $message = "You have been unsubscribed successfully.";
            }
        } else $message = "Not found contact!";

        return view('home.unsubscribe', ["message" => $message]);
    }

    public function shareFacebook(Request $request)
    {
        $link = urlencode($request->input('url'));
        return view('home.share_facebook', ["url" => $link]);
    }

    public function testplayer()
    {
        return view('test');
    }

    public function status(Request $request, $token = "")
    {
        $twitch = SocialAccount::where("access_token", $token)->first();
        $userByCode = User::where("code", $token)->first();
        $cooldown = $request->input('cooldown');
        if (!in_array($cooldown, [1, 2, 3, 4])) $cooldown = 0;
        $chanel = "";
        $folderServer = strtolower(config("aws.folder_client"));
        $links3 = config("aws.linkS3BoomMeter");
        $path = URL::to('/') . "/boom_status/image_gif/";
        $pathDefault = $links3 . "default/";
        $cssLink = $pathDefault . "style.css";
        $isDefault = true;
        $version = "";
        if ($twitch != null) {
            $user = User::find($twitch->user_id);
            $chanel = $user->name;
        }
        if ($userByCode != null) {
            $chanel = $userByCode->name;
            $boomMeter = BoomMeter::where("user_code", $token)
                ->where("status", 1)
                ->first();
            if ($boomMeter != null) {
                $isDefault = false;
                $version = "?" . $boomMeter->timestamp;
                $links3 = config("aws.linkS3BoomMeter") . $folderServer . "/";
                if ($boomMeter->custom_img) {
                    $path = $links3 . $boomMeter->user_code . "/";
                }
                if ($boomMeter->custom_style) {
                    $cssLink = $links3 . $boomMeter->user_code . "/" . "style.css";
                }
            }
        }
        $cssContent = file_get_contents($cssLink);
        return view('boom.boom', ["chanel" => $chanel,
            "cooldown" => $cooldown, "path" => $path,
            "cssLink" => $cssLink, "isDefault" => $isDefault,
            "cssContent" => $cssContent,
            "version" => $version]);
    }

    public function showDownload(Request $request)
    {
        return view('home.download');
    }

    public function status_webm(Request $request, $token = "")
    {
        $twitch = SocialAccount::where("access_token", $token)->first();
        $chanel = "";
        if ($twitch != null) {
            $user = User::find($twitch->user_id);
            $chanel = $user->name;
        }
        return view('boom.boom_webm', ["chanel" => $chanel]);
    }


    public function faq()
    {
        return view('home.faq');
    }

    public function play360()
    {
        return view('test.play360');
    }

    public function dmca()
    {
        return view('home.dmca');
    }

    public function showContact()
    {
        return view('home.contact');
    }

    public function showPrivacy()
    {
        return view('home.privacy');
    }

    public function showTerm()
    {
        return view('home.terms');
    }

    public function reLogin(Request $request)
    {

        $redirect_uri = $request->input('redirect_uri');
        if (!Helper::checkUrlDomain($redirect_uri) && $redirect_uri != null) {
            return redirect()->to(route('relogin'));
        }
        return view('errors.relogin', ['redirect_uri' => $redirect_uri]);
    }

    public function status2(Request $request, $code = "")
    {
        $folderServer = strtolower(config("aws.folder_client"));
        $links3 = config("aws.linkS3BoomMeter");
        $path = $links3 . "default/";
        $pathDefault = $path;
        $cssLink = $path . "style.css";
        //check code in boom_meter table => custom
        $boomMeter = BoomMeter::where("user_code", $code)
            ->where("status", 1)->first();
        $active = 0;
        $version = "";
        if ($boomMeter != null) {
            $version = "?" . $boomMeter->timestamp;
            $links3 = config("aws.linkS3BoomMeter") . $folderServer . "/";
            if ($boomMeter->custom_img) {
                $path = $links3 . $boomMeter->user_code . "/";
            }
            if ($boomMeter->custom_style) {
                $cssLink = $links3 . $boomMeter->user_code . "/" . "style.css";
            }
            $active = $boomMeter->status;
        }

        $cssContent = file_get_contents($cssLink);
        return view('boom.boom2', ["code" => $code,
            "path" => $path, "cssLink" => $cssLink,
            "cssContent" => $cssContent,
            "pathDefault" => $pathDefault,
            "active" => $active, "version" => $version]);
    }

    public function status3(Request $request)
    {
        $folderServer = strtolower(config("aws.folder_client"));
        $links3 = config("aws.linkS3BoomMeter");
        $path = $links3 . "default/";
        $pathDefault = $path;
        $cssLink = $path . "style.css";
        //check code in boom_meter table => custom
        $active = 0;
        $version = "";
        $pathOver = URL::to('/') . "/boom_status/BoomOL_BlackRed.png";
        $cssContent = file_get_contents($cssLink);
        return view('boom.boom3', [
            "path" => $path, "cssLink" => $cssLink,
            "cssContent" => $cssContent,
            "pathDefault" => $pathDefault,
            "pathOver" => $pathOver,
            "active" => $active, "version" => $version]);
    }

    public function getVrbeta(Request $request){
        if (config('app.env') == "production"){
            if ($request->server('HTTP_X_FORWARDED_PROTO') == "http"){
                $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
                return redirect()->to($rediect_uri);
            }
        }
        return view('home.vrbeta');
    }

    public function postVrbeta(Request $request){
        $email = $request->input('email');
        $twitch_id = $request->input('twitch_id');
        if ($email != "" && $twitch_id != "") {
            $check = VrbetaUser::where('email', $email)->first();
            if ($check == null) {
                $item = new VrbetaUser();
                $item->email = $email;
                $item->twitch_id = $twitch_id;
                $item->save();
                $code = uniqid() . $item->id;
                $item->code = $code;
                $item->save();
                //Helper::sendMailChimpSubcribeVrbeta($email, $twitch_id);
                return response()->json(['status' => 0, 'message' => Lang::get('vrbeta.success_signup')]);
            } else {
                return response()->json(['status' => 1, 'message' => Lang::get('vrbeta.already_signup')]);
            }
        } else
            return response()->json(['status' => 1, 'message' => Lang::get('vrbeta.error_signup')]);
    }
}
