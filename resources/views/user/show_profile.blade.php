@extends('layouts.master')
@section('title', 'Boom '.$user->name.' profile')
@section('ogtype', 'article')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')

<style>
.user_link_youtube {
    background-color: #bb0000 !important;
}
.user_link_youtube:hover {
    background-color: #bb0000 !important;
}
</style>

    <main>
        <div class="gradient">
            <div class="background"></div>
        </div>

        <!-- PROFILE CARD START -->
        <div class="profile_card">
            <div class="profile_user">
                <div class="profile_user_info">
                    @if($user->avatar)
                        <img class="profile_user_logo" src="{{$user->avatar}}">
                    @else
                        <img class="profile_user_logo" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-profile.png'}}">
                    @endif
                    <div class="profile_user_context">
                        <div class="user_context_tabs">
                            <a class="user_context_name" href="{{route('profile',[$user->name])}}">{{$user->displayname}}</a>
                            <div class="user_context_socials">
                                @if ($user->twitter_link)
                                    <a target="_blank" class="user_context_social" href="{{\App\Helpers\Helper::generate_twitter_follow($user->twitter_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"></a>
                                @endif
                                @if ($user->reddit_link)
                                    <a target="_blank" class="user_context_social" href="{{\App\Helpers\Helper::generate_reddit_follow($user->reddit_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/reddit-big.png'}}"></a>
                                @endif
                                @if($user->facebook_link)
                                    <a target="_blank" class="user_context_social" href="{{\App\Helpers\Helper::generate_facebook_follow($user->facebook_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}"></a>
                                @endif
                            </div>
                            {{--<div class="user_context_edit">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/edit.png'}}">
                                Edit
                            </div>--}}
                            @if($showClaim)
                            <a class="claim_user" href="{{route('oauth')}}?is_claim=1&source=0&username={{$user->name}}&type={{$type}}">
                            Claim page</a>
                            @endif
                            
                        </div>
                        <div class="user_context_about">{{$user->des}}</div>
                    </div>
                    
                </div>
                <div class="profile_user_stats">
                    <div class="profile_user_links">
                        <a class="user_link user_link_{{$type}}" target="_blank" href="{{$linkProfile}}">
                            @if($type == "mixer")
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/mixer-profile.png'}}">
                            @elseif($type == "youtube")
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/youtube-profile.png?v=123'}}">
                            @else
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-profile.png'}}">
                            @endif
                            Follow
                        </a>
                        
                        @if($subscribed == "")
                            <a class="user_link user_link_{{$type}} subscribe_button_background" href="{{route('profile.subscribe',[$user->name,'subscribe'])}}" id="subscribe">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/boom_icon_white.png'}}">
                                Subscribe
                            </a>
                        @else
                            <a class="user_link user_link_{{$type}} subscribe_button_background" href="#">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/check-mark-16.png'}}">
                                Subscribed
                            </a>
                        @endif

                        {{--@if(auth()->check())
                            <a class="user_link_discord" href="{{route('discordapp.login')}}">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/Discord-Logo-White.png'}}">
                                Discord
                            </a>
                        @endif--}}
                    </div>
                    <div class="profile_user_stats_box">
                        <div class="profile_user_stat views">
                            <div class="user_stat_title">Views</div>
                            <div class="user_stat_number"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">@if($user_extend) {{number_format($user_extend->view_numb)}} @else 0 @endif </div>
                        </div>
                        <div class="profile_user_stat">
                            <div class="user_stat_title">Followers</div>
                            <div class="user_stat_number">@if($user_extend) {{number_format($user_extend->follower_numb)}} @else 0 @endif</div>
                        </div>
                        <div class="profile_user_stat">
                            <div class="user_stat_title">Following</div>
                            <div class="user_stat_number">@if($user_extend) {{number_format($user_extend->following_numb)}} @else 0 @endif</div>
                        </div>
                        {{--<div class="profile_user_stat">
                            <div class="user_stat_title">Subscribers</div>
                            <div class="user_stat_number">@if($user_extend) {{number_format($user_extend->subscriber_numb)}} @else 0 @endif </div>
                        </div>--}}
                    </div>
                </div>
            </div>
            @if ($user->steam || $user->battle || $user->lol)
            <div class="profile_game_accounts">
                <div class="game_accounts_title">Game Accounts</div>
                <div class="game_accounts_links">
                    @if($user->steam)
                    <div class="game_accounts_link">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/steam.png'}}">
                        <div class="game_accounts_name">steam</div>
                        <a class="game_accounts_account" title="{{$user->steam}}">{{$user->steam}}</a>
                    </div>
                    @endif
                    @if($user->battle)
                    <div class="game_accounts_link">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/battlenet.png'}}">
                        <div class="game_accounts_name">battle.net</div>
                        <a class="game_accounts_account" title="{{$user->battle}}">{{$user->battle}}</a>
                    </div>
                    @endif
                    @if($user->lol)
                    <div class="game_accounts_link">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/lol.png'}}">
                        <div class="game_accounts_name">LoL</div>
                        <a class="game_accounts_account" title="{{$user->lol}}">{{$user->lol}}</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        <!-- PROFILE CARD ENDING -->

        @include('user.video',['listgame'=>$listgame,'userGameList'=>$userGameList])

        <div class="modal_big @if($subscribe != "" && !$loggedin) show @endif">
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
                    <a class="modal_login_btn twitch" href="{{\App\Helpers\Helper::createLoginUrl()}}">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-twitch.png">
                        Sign in with Twitch
                    </a>
                    <a class="modal_login_btn mixer" href="{{\App\Helpers\Helper::createLoginUrl('mixer')}}">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-mixer.png">
                        Sign in with Mixer
                    </a>
                    <a class="modal_login_btn google" href="{{\App\Helpers\Helper::createLoginUrl('youtube')}}">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver')}}/modal-google.png">
                        Sign in with Google
                    </a>
                </div>
                <div class="modal_footer">
                    <div class="modal_footer_body">Emails sent 1 per week to your inbox.<br>We DO NOT share your email with anyone.</div>
                </div>
            </div>
        </div>
    </main>
@endsection
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
        <div class="card_stats_new video_timezone">
                <span class="video_timezone_left">[%:date_time_zone%]</span>
                <span class="video_timezone_right">[%:hour_time_zone%]</span>
        </div>
        </div>
  [%/for%]
</script>
<script type="text/javascript">
var page_user_id= "{{$user->id}}";
var url_video_user_filer = "{{route("uservideos",['uid'=>$user->id])}}";
$(document).ready(function(){
    $(".model_close").click(function(){
         $('.modal_big').removeClass('show');        
    });
});    
</script>
{!! Html::script(config('content.cloudfront').'/slick/slick.min.js') !!}
{!! Html::script('/js/load_data/slick-responsive.js') !!}
{!! Html::script('/js/load_data/init_video_user.js?v=20170516') !!}
@endpush