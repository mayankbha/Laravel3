@if(isset($next_streams_video))
<style>
.overlay {
    z-index: 99;
    overflow: auto;
    background: #000;
    opacity:0.9;
    position: absolute;
    max-width:920px;
    width:calc(100% - 20px);
    margin: 0 auto 40px;
    display: none;
}
.replay_div {
    width: 15%;
    float: right;
}
.replay_div_icon {
    
}
.replay_div_text {
    font-size: 16px;
    margin-left: 32px;
    margin-top: 7px;
}
.replay_text {
    color: #fff;
}
.jw-icon-display::before {
    content: "";
    font-size: 30px;
    color: #fff;
    position: absolute;
    cursor: pointer;
}
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
}
.btn-facebook {
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
}
.modal_two_title_mid{
    color: #eaeced;
    font-family: Helvetica;
    font-size: 24px;
    font-weight: 400;
    margin-bottom: 21px;
    text-align: center;
    margin-top: 60px;
}
.modal_two_main {
    margin-top: 20px;
}
.video_list_div {
    width: 100%;
    margin-bottom: 20px;
    margin-top: 120px;
}
.video_list_div .card_image {
    height: 104px !important;
}
.video_list_div .list-video {
    margin: 6px auto !important;
}
.progress-circle {
    position: absolute;
    top: 7px;
    left: 40px;
    color: #fff;
    z-index: 111;
}
.play_icon {
    position: absolute;
    top: 26px;
    left: 60px;
    color: #fff;
    z-index: 111;
    display: none;
}
.play {
    width: 60%;
}
.gmpc-percent-text {
    left: -70px;
    position: absolute;
    top: -56px;
    width: 122px;
    z-index: 111;
    color: #fff;
}
.next_text {
    color: #fff;
    font-size: 20px;
    margin-left: 40px;
}
.game_name {
    background-color: #222431;
    margin-top: 4px;
}
.card_type {
    background-color: #222431 !important;
    margin-top: 0px !important;
    margin-left: 20px !important;
}
.eyes_img {
    width: 4%;
}
.card_overlay {
    margin: 0 10px !important;
    width: 180px !important;
}
.card_properties_overlay {
     padding: 4px 18px !important;
}
.card_stats_new_overlay {
    padding: 0 0 6px !important;
}
.card_views_new_overlay {
    margin-left: 14px !important;
}
.name_context_overlay {
    font-size: 14px !important;
}
.card_type_overlay {
    font-size: 14px !important;
}
.card_likes_new_overlay {
    font-size: 12px !important;
}
.card_views_new_overlay {
    font-size: 12px !important;
}
</style>
<script type="text/javascript">
    var vcode= "{{$vcode}}";
    var vtime= "{{$vtime}}";
    var player_type= "{{$player}}";
    var next_streams_video = "{{$next_streams_video}}";
</script>
@endif
<?php $video_user =  $video->user()->first();?>    
<div class="card_big video-detail">
    @include("player_detail.".$player, ["video"=>$video])

    @if($player != 'bitmovin')
        <style>
            #card_image {
                float:    left;
            }
        </style>

        <div class="overlay">
            <div class="replay_div">
                <div class="replay_div_icon">
                    <a class="jw-icon-display" href="javascript: void(0);" onclick="jwplayer().play();" title="Replay Video"></a>
                </div>

                <div class="replay_div_text">
                    <a class="replay_text" href="javascript: void(0);" onclick="jwplayer().play();" title="Replay Video">
                        Replay Video
                    </a>
                </div>
            </div>

            <div class="modal_two_title_mid">Your friends need to watch this!</div>

            <div class="modal_two_main">
                <div class="share-group">
                    <div class="share-bnt">
                        <a class="btn-twitter" onclick="shareThisTwitter(this);return false;" title="Share on Twitter" data-link="{{$video->link}}"
                           href="https://twitter.com/intent/tweet?original_referer={{$video->link}}&ref_src=twsrc%5Etfw&amp;text={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&tw_p=tweetbutton&amp;url={{$video->link}}&hashtags=boomtv">
                            Share on Twitter
                        </a>
                        <a class="btn-facebook" onclick="shareThisFacebook(this);return false;" data-link="{{$video->link}}"
                           href="https://www.facebook.com/sharer/sharer.php?u={{$video->link}}&src=sdkpreparse&title={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&sumary={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&quote=Watch the video  {{$video->link}} %23boomtv&hashtag=%23boomtv"
                           title="Share on Facebook">
                            Share on Facebook
                        </a>
                    </div>
                </div>
            </div>

            <div class="video_list_div">
                <div class="next_text">
                    Up Next
                </div>

                <div class="list-video" style="width: 838px !important;">
                    <div class="variable-width" data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}' id="view-trending2">
                        @include("home.video_overlay", ["videos" => $streams_video,"container"=>"view-trending2"])
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card_properties">
        <div class="card_profile">
            <div class="card_name">
                <a class="name_icon" href="{{route('profile',[$video_user->name])}}">
                    @if($video->user->avatar!=null)
                    <img src="{{$video_user->avatar}}">
                    @else
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
                    @endif
                </a>
                <a class="name_context video-title-nowrap" href="{{route('profile',[$video_user->name])}}">{{$video_user->displayname}}</a>
            </div>
            <a class="card_type" href="#">{{$video->getGameNames()}}</a>
        </div>
        @if(Auth::check() && Auth::id() == $video->user_id && $video->type == 3)  <!--monatge video -->
        <div class="btn-download-montage">
        <a href="{{config("aws.cloudfront").$video->links3}}" download>
            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/download.png'}}">
        </a>
        </div>
        @endif
        <div class="card_info">
            <div class="card_socials">
                
                <a class="card_social" title="Subscribe on this streamer" href="javascript:void(0);" id="subscribe" onclick="subscribe()" @if($subscribed != "") style="display:none" @endif>
                    <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/boom_icon_gray.png'}}"> 
                </a>
                
                <a class="card_social" title="Subscribed to this streamer" href="javascript:void(0);" id="subscribed" @if($subscribed == "") style="display:none" @endif>
                    <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/boom_icon_orange.png'}}">
                </a>
                
                <a class="card_social" onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?original_referer={{$video->link}}&ref_src=twsrc%5Etfw&amp;text={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&tw_p=tweetbutton&amp;url={{$video->link}}&hashtags=boomtv">
                    <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"> 
                </a>
                <a class="card_social" onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?u={{$video->link}}&src=sdkpreparse&title={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&sumary={{\App\Helpers\Helper::getRamdomTwitterShareText()}}&quote=Watch the video {{$video->link}} %23boomtv&hashtag=%23boomtv" title="Share on Facebook">
                    <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
                </a>
                
                @if($video_user->type == $video_user::USER_TYPE_TWITCH)
                <a class="card_social" target="_blank" href="{{$video_user->getProfileLink()}}" title="Go to Twitch stream">
                    <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-profile.png'}}">
                </a>
                @else
                <a  class="card_social" target="_blank" href="{{$video_user->getProfileLink()}}" title="Go to Mixer stream">
                    <img  class="card_social" src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/mixer-icon.png'}}">
                </a>
                @endif
            </div>
            <div class="card_statistics">
                <div class="card_likes" data-vcode="{{$video->code}}">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
                    <span id="like_numb">{{$video->like_numb}} </span>
                </div>
                <div class="card_views">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
                    <span id="view_numb">{{$video->getViewNumbSort()}} </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal_big">
    <div class="modal_big_container">
        <div class="tokens_header">
            <div class="tokens_title"></div>
            <a class="tokens_close model_close"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-close.png"></a>
        </div>
        <div class="modal_logins login2">
            <a class="nav_logo" href="#">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/logo.png">
            </a>
            <div class="modal_login_info">
                Signup to get video alerts from your favorite streamers
            </div>
            <a class="modal_login_btn twitch" href="{{\App\Helpers\Helper::createLoginUrlForSubscribe()}}">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-twitch.png">
                Sign in with Twitch
            </a>
            <a class="modal_login_btn mixer" href="{{\App\Helpers\Helper::createLoginUrlForSubscribe('mixer')}}">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-mixer.png">
                Sign in with Mixer
            </a>
            <a class="modal_login_btn google" href="{{\App\Helpers\Helper::createLoginUrlForSubscribe('youtube')}}">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-google.png">
                Sign in with Google
            </a>
        </div>
        <div class="modal_footer">
            <div class="modal_footer_body">Emails sent 1 per week to your inbox.<br>We DO NOT share your email with anyone.</div>
        </div>
    </div>
</div>

@push('content-javascript')
@endpush

<script type="text/javascript">
var page_user_id= "{{$video_user->id}}";
var loggedin = "{{Auth::check()}}";

function subscribe(){
    if(loggedin) {
        var request = $.ajax({
            url: "/subscriptions/subscribe",
            method: "GET",
            data: {
                streamer_id:page_user_id
            }
        });
        request.done(function (data) {
            if (data.status == 1){
                $("#subscribe").hide();
                $("#subscribed").show();
            }
        });

        request.fail(function (jqXHR, textStatus) {
            show_msg(textStatus, snackbar);
        });
    } else {
        $('.modal_big').attr('class','modal_big show');        
    }
}    

/*    
$(document).ready(function(){
    alert("I am in");
    $("#subscribe").click(function(){
        alert("A click");
        if(loggedin) {
            var request = $.ajax({
                url: "/subscriptions/subscribe",
                method: "GET",
                data: {
                    streamer_id:page_user_id
                }
            });
            request.done(function (data) {
                if (data.status == 1){
                    alert("success");
                }
            });

            request.fail(function (jqXHR, textStatus) {
                show_msg(textStatus, snackbar);
            });
        } else {
             $('.modal_big').attr('class','modal_big show');        
        }
    });
    $(".model_close").click(function(){
         $('.modal_big').removeClass('show');        
    });
});
*/    
    
    
</script>
