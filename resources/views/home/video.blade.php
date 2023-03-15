@foreach($videos as $key=> $video)
    <?php $video_user = $video->user()->first(); ?>
    @if($video_user)
    <div class="card" vcode="{{$video->code}}">
        <a class="card_image" href="{{route('playvideo').'?v='.$video->code}}">
            @if($video->type == 2)
            <label class="icon_360"></label>
            @endif
            <img src="{{config('aws.sourceLink').$video->thumbnail}}"
            onError='this.onerror=null;this.src="{{$imageDefault}}";'>
            <ul class="card_play">
                <li class="play_circle one"></li>
                <li class="play_circle two"></li>
                <li class="play_triangle"></li>
            </ul>
        </a>
        <ul class="card_properties">
            <li class="card_name">
                <a class="name_icon" href="{{route('profile',[$video_user->name])}}">
                    @if($video_user->avatar!=null)
                        <img src="{{$video_user->avatar}}">
                    @else
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
                    @endif
                </a>
                <a class="name_context video-title-nowrap"
                   href="{{route('profile',[$video_user->name])}}">{{$video_user->displayname}}</a>
            </li>
        </ul>
        <span class="card_stats_new">
            <a class="card_type video-title-nowrap" href="#" title="{{$video->getGameNames()}}">{{str_limit($video->getGameNames(),44,"...")}}</a>
        </span>
        <div class="card_stats_new">
            <div class="card_statistics">
                <div class="card_likes_new" data-vcode="{{$video->code}}">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
                    <span>{{$video->like_numb}} </span>
                </div>
                <div class="card_views_new">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
                    <span id="view_numb">{{$video->getViewNumbSort()}} </span>
                </div>
            </div>
            @if ($video_user && $video_user->id == Auth::id())
            <div class="stats_options">
                <a class="stats_options_btn" onclick="show_delete($(this))">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/dots.png'}}">
                </a>
                <a class="stats_options_flag">
                    <img class="tooltip_flag"
                         src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/flag.png'}}">
                </a>
                <a class="stats_options_delete" href="javascript:void(0);" data-vcode="{{$video->code}}" data-vid="{{$video->id}}" data-container="{{$container}}">Delete the video</a>
            </div>
            @endif

        </div>
    </div>
    @endif
@endforeach