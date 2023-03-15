@foreach($videos as $key=> $video)   
<li class="card">

        <a class="card_image" href="{{route('playvideo').'?v='.$video->code}}">
            @if($video->type==2)
            <label class="icon_360">360</label> 
            @endif
            <img src="{{config('aws.sourceLink').$video->thumbnail}}">
            <ul class="card_play">
                <li class="play_circle one"></li>
                <li class="play_circle two"></li>
                <li class="play_triangle"></li>
            </ul>

        </a>
        <ul class="card_properties">
            <li class="card_name">
                <a class="name_icon" href="{{route('home', ['view' => $view]).'?u='.$video->user_id}}">
                    @if($video->user()->first()->avatar!=null)
                    <img src="{{$video->user()->first()->avatar}}">
                    @else
                    <img id="no-ava" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}"/>
                    @endif
                    
                </a>
                <a class="name_context video-title-nowrap" href="{{route('home', ['view' => 'popular']).'?u='.$video->user_id}}">{{$video->user()->first()->displayname}}</a>

            </li>
            <a class="card_type video-title-nowrap" href="#" title="{{$video->getGameNames()}}">{{$video->getGameNames()}}</a>
            <br>
        <!--     <li class="card_date"></li> -->
            <li class="card_date">{{$video->formatTime($request)}}</li>
        </ul>
        <div class="card_stats">
            <div class="stats_views">{{$video->view_numb}} views</div>
            <div class="stats_shares">
                <a class="stats_shares_btn" href="#">
                    <div class="stats_shares_icon">
                        <img class="share_image" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/share.png'}}">
                        <img class="shared_image" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/shared.png'}}">
                    </div>
                    <div class="stats_shares_context">Share</div>
                </a>
                
                <div class="stats_shares_number">( {{$video->share_numb}} )</div>
                <div class="tooltip" id="tooltip-{{$video->id}}">
                    <a class="tooltip_btn" onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?original_referer={{$video->link}}&ref_src=twsrc%5Etfw&amp;text=&tw_p=tweetbutton&amp;url={{$video->link}}" >
                        <div class="tooltip_btn_text">Twitter</div>
                        <div class="tooltip_btn_image">
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/tt-twitter.png'}}">
                        </div>
                    </a>
                    <a class="tooltip_btn" onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?u={{$video->link}}&src=sdkpreparse" title="Share on Facebook">
                        <div class="tooltip_btn_text">Facebook</div>
                        <div class="tooltip_btn_image">
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/tt-facebook.png'}}">
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </li>
@endforeach
{!! Html::script('/js/load_video.js') !!}
