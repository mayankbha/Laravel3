@extends('layouts.master')
@section('title', 'Boom my profile')
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
                    @if(Auth::user()->avatar)
                        <img class="profile_user_logo" src="{{Auth::user()->avatar}}">
                    @else
                        <img class="profile_user_logo" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-profile.png'}}">
                    @endif
                    <div class="profile_user_context">
                        <div class="user_context_tabs">
                            <a class="user_context_name" href="{{route('profile',['name'=>auth()->user()->name])}}">{{Auth::user()->displayname}}</a>
                            <div class="user_context_socials">
                                <a target="_blank" class="user_context_social @if(!Auth::user()->twitter_link) x_hide @endif" name="twitter_link" href="{{\App\Helpers\Helper::generate_twitter_follow(Auth::user()->twitter_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"></a>
                                <div class="user_context_social_input"><input placeholder="Twitter username" type="text" value="{{Auth::user()->twitter_link}}" maxlength="{{config('input.profile.social_username')}}" name="twitter" /></div>

                                <a target="_blank" class="user_context_social @if(!Auth::user()->reddit_link) x_hide @endif"  name="reddit_link" href="{{\App\Helpers\Helper::generate_reddit_follow(Auth::user()->reddit_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/reddit-big.png'}}"></a>
                                <div class="user_context_social_input"><input placeholder="Reddit username" type="text" value="{{Auth::user()->reddit_link}}" maxlength="{{config('input.profile.social_username')}}" name="reddit" /></div>

                                <a target="_blank" class="user_context_social @if(!Auth::user()->facebook_link) x_hide @endif" name="facebook_link" href="{{\App\Helpers\Helper::generate_facebook_follow(Auth::user()->facebook_link)}}"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}"></a>
                                <div class="user_context_social_input"><input placeholder="Facebook username" type="text" value="{{Auth::user()->facebook_link}}" maxlength="{{config('input.profile.social_username')}}" name="facebook" /></div>
                            </div>
                            <div class="user_context_edit">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/edit.png'}}">
                                Edit
                            </div>
                            <div class="user_context_save none">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/edit.png'}}">
                                Save
                            </div>
                        </div>
                        <div class="user_context_about">{{Auth::user()->des}}</div>
                        <div class="user_context_about_input"><textarea name="des" rows="10" cols="70" maxlength="{{config('input.profile.description')}}">{{Auth::user()->des}}</textarea></div>
                    </div>
                </div>
                <div class="profile_user_stats">
                    <div class="profile_user_links">
                        <a class="user_link user_link_{{$type}}" target="_blank" href="{{$linkProfile}}">
                            @if($type == "mixer")
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/mixer-profile.png'}}">
                            @elseif($type == "youtube")
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/youtube-profile.png?v=1'}}">
                            @else
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-profile.png'}}">
                            @endif
                            Follow
                        </a>
                        {{--@if(auth()->check())
                            <a class="user_link_discord" href="{{route('discordapp.login')}}">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/Discord-Logo-White.png'}}">
                                Discord
                            </a>
                        @endif--}}
                    </div>
                    <style type="text/css">
                    </style>
                    <div class="profile_user_stats_box">
                        <div class="profile_user_stat views">
                            <div class="user_stat_title">Views</div>
                            <div class="user_stat_number"><img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}"> {{number_format($user_extend->view_numb)}}</div>
                        </div>
                        <div class="profile_user_stat">
                            <div class="user_stat_title">Followers</div>
                            <div class="user_stat_number">{{number_format($user_extend->follower_numb)}}</div>
                        </div>
                        <div class="profile_user_stat">
                            <div class="user_stat_title">Following</div>
                            <div class="user_stat_number">{{number_format($user_extend->following_numb)}}</div>
                        </div>
                        {{--<div class="profile_user_stat">
                            <div class="user_stat_title">Subscribers</div>
                            <div class="user_stat_number">{{number_format($user_extend->subscriber_numb)}}</div>
                        </div>--}}
                    </div>
                </div>
            </div>
            <div class="profile_game_accounts @if(!Auth::user()->steam && !Auth::user()->battle && !Auth::user()->lol) x_hide @endif">
                <div class="game_accounts_title">Game Accounts</div>
                <div class="game_accounts_links">
                    <div class="game_accounts_link @if(!Auth::user()->steam) x_hide @endif">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/steam.png'}}">
                        <div class="game_accounts_name">Steam</div>
                        <a class="game_accounts_account" title="{{Auth::user()->steam}}">{{Auth::user()->steam}}</a>
                        <div class="game_accounts_input"><input type="text" value="{{Auth::user()->steam}}" maxlength="{{config('input.profile.game_account')}}" name="steam" /></div>
                    </div>
                    <div class="game_accounts_link @if(!Auth::user()->battle) x_hide @endif">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/battlenet.png'}}">
                        <div class="game_accounts_name">Battle</div>
                        <a class="game_accounts_account" title="{{Auth::user()->battle}}">{{Auth::user()->battle}}</a>
                        <div class="game_accounts_input"><input type="text" value="{{Auth::user()->battle}}" maxlength="{{config('input.profile.game_account')}}" name="battle" /></div>
                    </div>
                    <div class="game_accounts_link @if(!Auth::user()->lol) x_hide @endif">
                        <img class="game_accounts_icon" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/lol.png'}}">
                        <div class="game_accounts_name">LoL</div>
                        <a class="game_accounts_account" title="{{Auth::user()->lol}}">{{Auth::user()->lol}}</a>
                        <div class="game_accounts_input"><input type="text" value="{{Auth::user()->lol}}" maxlength="{{config('input.profile.game_account')}}" name="lol" /></div>
                    </div>
                </div>
            </div>
        </div>
        @include('user.video',['listgame'=>$listgame,'userGameList'=>$userGameList])

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
        </div>
  [%/for%]
</script>
<script type="text/javascript">
    var page_user_id= "{{Auth::id()}}";
    var url_video_user_filer = "{{route("uservideos",['uid'=>Auth::id()])}}";
    var discord_msg = "{{$discord_msg}}";
    $(document).ready(function(){
        if (discord_msg != ""){
            show_msg(discord_msg,'snackbar');
        }
    });
</script>
{!! Html::script(config('content.cloudfront').'/slick/slick.min.js') !!}
{!! Html::script('/js/load_data/slick-responsive.js') !!}
{!! Html::script('/js/load_data/init_video_user.js') !!}
{!! Html::script('/js/profile/main.js') !!}
@endpush