@extends('layouts.master')
@section('title', 'Boom team')
@section('ogtype', 'article')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main>
        <div class="gradient">
            <div class="background"></div>
        </div>
        <!-- CONTENT START -->
            <div class="team_content">

    <!-- TEAM PANE START -->
                {{ Form::open(array('url'=>route('changeBanner').'?teamname='.$team->name,'files'=>true, 'class' => 'form-horizontal')) }}
                {!! csrf_field() !!}
                <div class="team_pane">
                    <div class="team_pane_about">
                        <div class="team_info">
                            <div class="team_name video-title-nowrap" title="{{$team->name}} Team">{{$team->name}} Team</div>
                            <div class="card_socials show-icon-link">
                                @if($team->website != "")
                                <a target=”_blank” class="card_social" href="{{$team->website}}">
                                    <img  src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/website_link.png'}}">
                                </a>
                                @endif
                                @if($team->twitch_link != "")
                                <a target=”_blank” class="card_social" href="{{$team->twitch_link}}">
                                    <img  src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/twitch-icon.png'}}">
                                </a>
                                @endif
                                @if($team->twitter_link != "")
                                <a target=”_blank” class="card_social" href="{{$team->twitter_link}}">
                                    <img  src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}">
                                </a>
                                @endif
                                @if($team->facebook_link != "")
                                <a target=”_blank” class="card_social" href="{{$team->facebook_link}}">
                                    <img  src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
                                </a>
                                @endif
                            </div>
                            @if(Auth::check())
                                <div class="card_socials auth-icon-link">
                                    <div class=" user_context_social_input" style="display: block;"><input placeholder="Website" type="text" maxlength="100" name="website" value="{{$team->website}}"></div>
                                    <div class="user_context_social_input" style="display: block;"><input placeholder="Twitch link" type="text" maxlength="100" name="twitch_link" value="{{$team->twitch_link}}"></div>
                                </div>
                                <div class="card_socials auth-icon-link">
                                    <div class="user_context_social_input" style="display: block;"><input placeholder="Twitter link" type="text" maxlength="100" name="twitter_link" value="{{$team->twitter_link}}"></div>
                                    <div class="user_context_social_input" style="display: block;"><input placeholder="Facebook link" type="text" maxlength="100" name="facebook_link" value="{{$team->facebook_link}}"></div>
                                </div>
                                
                            @endif
                            <div class="team_number">{{$total}} streamers</div>
                        </div>
                        
                        <div class="team_banner">
                            <img src="{{$banner}}" id="banner-image">
                        </div>
                        @if(Auth::id() == $team->owner_id)
                        <div class="edit-banner-team">
                            <div class="user_context_edit">
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/edit.png'}}">
                                <div class="file_button_container">
                                
                                
                                <input type="file" name="banner" id="banner-file"/>
                                <button type="submit" class="upload-banner-save" value="Save">Save</button>
                                
                                </div>
                                <span class="upload-banner-text">Upload</span>
                                
                                
                            </div>

                        </div>

                        @endif
                    </div>
                    <div class="team_member_list">
                    @foreach($members as $member)
                        <a class="team_member" href="{{route('profile',[$member->user->name])}}" title="{{$member->user->name}}">
                            <div class="team_member_subs">
                                <img src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/subscribers-icon.png'}}">
                                {{$member->subscriber_numb}}
                            </div>
                            <div class="team_member_icon">
                                @if($member->user->avatar!=null)
                                <img src="{{$member->user->avatar}}">
                                @else
                                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
                                @endif
                            </div>
                            <div class="team_member_name video-title-nowrap">{{$member->user->displayname}}</div>
                        </a>
                    @endforeach
                    </div>
                    <!-- <div class="types_team_sort">
                        <div class="types_team_info">
                            <div class="types_team_name">Filter by:</div>
                            <a class="team_sort_button top">Streamer</a>
                        </div>
                        
                        <div class="team_sort_dropdown">
                            <a class="team_sort_button">Date</a>
                            <a class="team_sort_button">Views</a>
                        </div>
                    </div> -->
                </div>
                {{ Form::close() }}
    <!-- TEAM PANE ENDING -->
            <!-- CAROUSEL START -->
                @include('team.videos',['members'=>$members])
            <!-- CAROUSEL ENDING -->
            </div>
<!-- CONTENT END -->
    </main>
@endsection
@push('content-javascript')
{!! Html::script('/jsrender/jsrender.min.js') !!}
<script type="text/javascript">
    var teamId = "{{$team->owner_id}}";
</script>
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
            [%if (auth || is_leader_team)%]
            <div class="stats_options">
                <a class="stats_options_btn" onclick="show_delete($(this))">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/dots.png'}}">
                </a>
                <a class="stats_options_flag">
                    <img class="tooltip_flag"
                         src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/flag.png'}}">
                </a>
                [%if (auth) %]
                <a class="stats_options_delete" href="javascript:void(0);" data-vcode="[%:code%]" data-vid="[%:id%]" data-container="[%:container%]">Delete the video</a>
                [%/if%]
                [%if (auth && is_leader_team) %]
                <a class="stats_options_download" href="[%:links3%]" download>Download the video</a>
                [%else%]
                <a class="stats_options_download" href="[%:links3%]" download style="top:15px">Download the video</a>
                [%/if%]
            </div>
            [%/if%]

        </div>
        </div>
  [%/for%]
</script>
<script type="text/javascript">
    var url_video_user_filer = "{{route('uservideos')}}";
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#banner-image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#banner-file").change(function(){
        readURL(this);
    });

    $(document).ready(function(){
        $(".user_context_edit").on('click',function(e){
            $(".show-icon-link").hide();
            $(".auth-icon-link").show();
            $(".file_button_container").show();
            $(".upload-banner-text").show();
        });
    });
</script>
{!! Html::script(config('content.cloudfront').'/slick/slick.min.js') !!}
{!! Html::script('/js/load_data/slick-responsive.js') !!}
{!! Html::script('/js/load_data/init_video_team.js') !!}
@endpush