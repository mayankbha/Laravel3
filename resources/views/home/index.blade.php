@extends('layouts.master')
@if(!$isDetail)
    @section('title', 'Boom Game Videos')
@section('ogtype', 'article')
@section('ogurl', route('home',['view'=>'popular']))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@else
    @if($videoDetail->type==3)
        @section('title', $videoDetail->title.' - '.$videoDetail->user->name)
@else
    @section('title', $videoDetail->getGameNames().' - '.$videoDetail->user->name)
@endif
@section('ogtype', 'video.other')
@section('ogurl', $videoDetail->link)
@section('ogimage', config('aws.sourceLink').$videoDetail->thumbnail)
@section('ogvideo', config("aws.sourceLink").$videoDetail->links3)
@section('description', "BOOM.TV - Boom Game Videos")
@section('embed_player', route('embed',$videoDetail->code))
@endif
@section('content')

    <main>
        <div class="gradient">
            <div class="background"></div>
        </div>
        @if($alertFail)
            <script type="text/javascript">
                $(document).ready(function () {
                    var mss = "{{$message}}";
                    show_msg(mss, "alert_fail");
                });
            </script>
            <div id="alert_fail"></div>
        @endif
        @if(!$isDetail)
            <div class="variable-width-big center" id="view-carousel">
                @include("home.carousels", ["videos" => $carousels,])
            </div>
        @else
            @include("home.video_detail",["video"=>$videoDetail,"next_video"=>$next_video,
            "player" => $player, "linkHlsLocal" => $linkHlsLocal, "subscribed" => $subscribed])
        @endif

        @if(isset($ref) && $ref == 'share')
            <style>
                .btn-twitter {
                    background-color: #60a9ec; /* Green */
                    border: none;
                    color: white;
                    padding: 8px 15px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 20px;
                    margin: 4px 2px;
                    cursor: pointer;
                    border-radius: 5px;
                    min-width: 220px;
                }.btn-facebook {
                    background-color: #4c5ea3; /* Green */
                    border: none;
                    color: white;
                    padding: 8px 15px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 20px;
                    margin: 4px 2px;
                    cursor: pointer;
                    border-radius: 5px;
                    min-width: 220px;
                }.card_big {
                    margin-bottom: 20px;
                }
            </style>
            <div class="list-video">
                <div class="share-group">
                    <div class="share-bnt">
                        <a class="btn-twitter" onclick="shareThisTwitter(this);return false;" title="Share on Twitter" data-link="{{$videoDetail->link}}"
                           href="https://twitter.com/intent/tweet?original_referer={{$videoDetail->link}}&ref_src=twsrc%5Etfw&amp;text={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&tw_p=tweetbutton&amp;url={{$videoDetail->link}}&hashtags=boomtv">
                            Share on Twitter
                        </a>
                        <a class="btn-facebook" onclick="shareThisFacebook(this);return false;" data-link="{{$videoDetail->link}}"
                           href="https://www.facebook.com/sharer/sharer.php?u={{$videoDetail->link}}&src=sdkpreparse&title=Watch the montage&sumary=Watch the montage&quote=Watch the montage {{$videoDetail->link}} %23boomtv&hashtag=%23boomtv"
                           title="Share on Facebook">
                            Share on Facebook
                        </a>
                    </div>
                    <p class="note-share">Show your followers some love. Post your montage. #boomtv</p>
                </div>

            </div>
        @else
            <div class="list-video">
                <div class="carousel_title">Trending</div>
                <div class="variable-width"
                     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                     id="view-trending">
                    @include("home.video", ["videos" => $trending,"container"=>"view-trending"])
                </div>
                <div class="carousel_title">Recent</div>
                <div class="variable-width"
                     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                     id="view-recent">

                </div>
                <div class="carousel_title">Highlights</div>
                <div class="variable-width"
                     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                     id="view-highlight">

                </div>
                <div class="carousel_title">360 Videos</div>
                <div class="variable-width"
                     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                     id="view-video360">

                </div>
                @foreach($listgame as $key => $game)
                    <div class="carousel_title" data-id="{{$game->id}}">More of {{$game->name}}</div>
                    <div class="variable-width game-category"
                         data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                         id="view-game_{{$game->id}}">
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
@push('ga-javascript')
<script>
    function shareThisFacebook(item) {
        ga('send', 'event', 'SHARE_MONTAGE_FACEBOOK', 'click', $(item).data('link'));
        var link = $(item).attr('href');
        var popup = window.open(link, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
        var pollTimer = window.setInterval(function () {
            if (popup.closed !== false) { // !== is required for compatibility with Opera
                window.clearInterval(pollTimer);
                getShare();
            }
        }, 200);
    }
    function shareThisTwitter(item) {
        ga('send', 'event', 'SHARE_MONTAGE_TWITTER', 'click', $(item).data('link'));
        var link = $(item).attr('href');
        var popup = window.open(link, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
        var pollTimer = window.setInterval(function () {
            if (popup.closed !== false) { // !== is required for compatibility with Opera
                window.clearInterval(pollTimer);
                getShare();
            }
        }, 200);
    }
</script>
@endpush
@push('content-javascript')
{!! Html::script('/jsrender/jsrender.min.js') !!}
<script id="video_item_tpl" type="text/x-jsrender">
  [%for content%]
        <div class="card" vcode="[%:code%]">
        <a class="card_image" href="[%:link%]">
        [%if (type==2)%]
            <label class="icon_360"></label>
        [%/if%]
        <img src="[%:thumbnail%]"
            onError='this.onerror=null;this.src="[%:default_image%]";'>
            <ul class="card_play">
                <li class="play_circle one"></li>
                <li class="play_circle two"></li>
                <li class="play_triangle"></li>
            </ul>
        </a>
        <ul class="card_properties">
            <li class="card_name">
                <a class="name_icon" href="[%:user_profile%]">
                    <img src="[%:user_avatar%]">
                </a>
        <a class="name_context video-title-nowrap"
           href="[%:user_profile%]">[%:user_displayname%]</a>
            </li>
            <a class="card_type video-title-nowrap" href="#"
               title="[%:game_name%]">[%:game_name_display%]</a>
        </ul>
        <div class="card_stats_new">
            <div class="card_statistics">
                <div class="card_likes_new" data-vcode="[%:code%]">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
                    <span>[%:like_numb%] </span>
                </div>
                <div class="card_views_new">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
                    <span id="view_numb">[%:view_numb%]</span>
                </div>
            </div>
            [%if (auth)%]
            <div class="stats_options">
                <a class="stats_options_btn" onclick="show_delete($(this))">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/dots.png'}}">
                </a>
                <a class="stats_options_flag">
                    <img class="tooltip_flag"
                         src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/flag.png'}}">
                </a>
                <a class="stats_options_delete" href="javascript:void(0);" data-vcode="[%:code%]" data-vid="[%:id%]" data-container="[%:container%]">Delete the video</a>
            </div>
            [%/if%]

        </div>
        </div>
  [%/for%]

</script>
{!! Html::script(config('content.cloudfront').'/slick/slick.min.js') !!}
{!! Html::script('/js/load_data/slick-responsive.js?v=201706151') !!}
{!! Html::script('/js/load_data/init.js?v=201708101') !!}
{!! Html::script('/js/grasp_mobile_progress_circle-1.0.0.js?v=201706161') !!}
@endpush

