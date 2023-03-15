<div class="card" vcode="[vcode]">
    <a class="card_image" href="[link]">

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
            <a class="name_icon" href="#">
                @if($video->user()->first()->avatar!=null)
                    <img src="{{$video_user->avatar}}">
                @else
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
                @endif
            </a>
            <a class="name_context video-title-nowrap"
               href="{{route('profile',[$video_user->displayname])}}">{{$video_user->displayname}}</a>
        </li>
        <a class="card_type video-title-nowrap" href="#"
           title="{{$video->getGameNames()}}">{{str_limit($video->getGameNames(),44,"...")}}</a>
    </ul>
    <div class="card_stats_new">
        <div class="card_statistics">
            <div class="card_likes_new" data-vcode="{{$video->code}}">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
                <span>{{$video->like_numb}} </span>
            </div>
            <div class="card_views_new">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
                <span id="view_numb">{{$video->view_numb}} </span>
            </div>
        </div>


    </div>
</div>