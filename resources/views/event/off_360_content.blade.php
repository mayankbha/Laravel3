@extends('event.layout')
@section('title', $event->title)
@section('ogtype', 'video')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main class="events">
        <div class="gradient">
            <div class="background"></div>
        </div>
        <div class="events_title">
            <div class="title_main_context">Live Event: </div>
            <div class="title_about">{{$event->title}}</div>
        </div>
        <div class="events_info_container">
            <div class="events_info_banner"></div>
            <div class="events_info">
                <div class="events_videos">
                    <div class="videos_normal">
                        <div class="videos_switch normal" onclick="changeMode(this)">
                            <div class="videos_switch_setting normal">Jumbotron</div>
                            <div class="videos_switch_box">
                                <div class="videos_switch_button"></div>
                            </div>
                            <div class="videos_switch_setting compact">Compact</div>
                        </div>
                        @if($show_jumbo)
                            <div class="vidoe_small_container">
                                <input type="hidden" name="link-jumb" id="link-jumb" value="{{$event->sub_stream_url}}" />
                                <input type="hidden" name="poster-jumb" id="poster-jumb" {{$event->sub_stream_poster}} />
                                <div id="video-jumb"></div>
                                <div class="video_small_normal_border"></div>
                            </div>
                        @endif
                    </div>
                    <div class="card_small">
                        {{--<input type="hidden" id="link" name="link" value="{{$event->main_stream_url}}" />
                        <input type="hidden" id="link-poster" name="link-poster" value="{{$event->main_stream_poster}}" />
                        <div class="card_image"  href="#">
                            <div id="card_image" style="width: 100%"></div>
                            <div class="overlay_360_container">
                                <div class="overlay_360">
                                    <img class="overlay_360_icon" src="/assets/v1/drag-rotate.png">
                                    <div class="overlay_360_text">Drag to rotate</div>
                                </div>
                            </div>
                            <img class="questionmark" src="/assets/v1/questionmark.png" onmouseover="showOverlay()" onmouseleave="hideOverlay()">
                        </div>
                        <div class="card_properties">
                            <div class="card_profile">
                                <div class="card_name video-title-nowrap">
                                    <a class="name_icon" href="#">
                                        <img src="/assets/v1/icon-1.png">
                                    </a>
                                    <a class="name_context" href="#">{{$event->username}}</a>
                                </div>
                                <a class="card_type" href="#">{{$event->game_name}}</a>
                            </div>
                            <div class="card_info">
                                <div class="card_socials">
                                    <a class="card_social" onclick="share(this);return false;" title="Share on Twitter" href="{{\App\Helpers\Helper::generate_twitter_share_link()}}">
                                        <img  src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"> </a>
                                    <a class="card_social" onclick="share(this);return false;" title="Share on Facebook" href="{{\App\Helpers\Helper::generate_facebook_share_link()}}">
                                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
                                    </a>
                                    <a  class="card_social" target="_blank" href="{{\App\Helpers\Helper::generate_twitch_profile_link($event->username)}}" title="Go to Twitch stream">
                                        <img  src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-profile.png'}}">
                                    </a>
                                </div>
                                <div class="card_statistics">
                                    <div class="card_likes">
                                        <img src="/assets/v1/liked.png" data-id="{{$event_object->id}}">
                                        <span id="like_numb">{{$event_object->like_numb}}</span>
                                    </div>
                                    <div class="card_views">
                                        <img src="/assets/v1/view.png">
                                        <span id="view_numb">{{$event_object->view_numb}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                    </div>
                </div>
                <div class="events_context">
                    <div class="ad_unit">
                        <img class="ad_logo" src="/assets/v1/ad-logo.png">
                        <div class="ad_context">ESL, a part of the in ternational digital entertainment group MTG, is the world's largest esports company, leading the industry across the most popular video games with numerous online and offline.</div>
                    </div>
                    {{--<div class="map" style="display: none">
                        <div class="map_image">
                            @foreach($map_config->camera as $key=>$item)
                                @if($key == 0)
                                    @php($map_default_name = $item->name)
                                    <div class="map_spot selected" data-stream='{"url":"{{$item->url}}","name":"{{$item->name}}","key":"{{$key}}"}' onclick="mapSpots(this)" style="left: {{$item->x}}%;top: {{$item->y}}%;"></div>
                                @else
                                    <div class="map_spot" data-stream='{"url":"{{$item->url}}","name":"{{$item->name}}","key":"{{$key}}"}' onclick="mapSpots(this)" style="left: {{$item->x}}%;top: {{$item->y}}%;"></div>
                                @endif
                            @endforeach
                            <img src="{{'/assets/'.'map'.'/'.$map_config->map}}">
                            <div class="minimap-overlay"></div>
                        </div>
                        <div class="map_info">
                            <div class="map_name">{{$map_default_name}}</div>
                            <div class="map_help">Click on dots to change camera angle</div>
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
        @include('event.inside_footer')
    </main>
@endsection
@push('content-javascript')
<style>
    .videos_switch_right {
        height: 100%;
        width: 85px;
        background-color: #222431;
        padding: 46px 0;
        cursor: pointer;
        float: right;
    }
    .videos_switch{
        float: left;
    }
    .videos_switch.normal .videos_switch_setting.normal {
        color: #fff;
    }
    .videos_switch.compact .videos_switch_setting.compact {
        color: #fff;
    }
    .videos_switch.normal .videos_switch_setting.normal {
        color: #ffffff;
    }
    .videos_switch.normal .videos_switch_button:before {
        transform: none;
    }
    .videos_switch.compact .videos_switch_setting.compact {
        color: #ffffff;
    }
    .videos_switch.compact .videos_switch_button:before {
        transform: translate(0,100%);
    }

    .videos_switch_right.normal .videos_switch_setting.normal {
        color: #fff;
    }
    .videos_switch_right.compact .videos_switch_setting.compact {
        color: #fff;
    }
    .videos_switch_right.normal .videos_switch_setting.normal {
        color: #ffffff;
    }
    .videos_switch_right.normal .videos_switch_button:before {
        transform: none;
    }
    .videos_switch_right.compact .videos_switch_setting.compact {
        color: #ffffff;
    }
    .videos_switch_right.compact .videos_switch_button:before {
        transform: translate(0,100%);
    }
    .minimap-overlay{
        position: absolute;
        width: 100%;
        height:100%;
        z-index: 100;
        top : 0;
        left:0;
        display: block;
    }
</style>
<script>
    var event_stream_id = '';
    var show_jumbo = parseInt('{{$show_jumbo}}');
    var default_player = '{{$default_player}}';
    var current_map = '{{$current_map}}';
    var stream_input = '{{$input}}';
    var stream_quality = '{{$quality}}';
    var caster_mode_interval_time = parseInt('{!! config("esea.casterModeIntervalTime") !!}');
</script>
{!! Html::script('/bitmoviplayer_js/bitmovinplayer.js') !!}
{!! Html::script(config('content.cloudfront') . '/jwplayer/'.config('content.jwplayer_ver').'/jwplayer.js') !!}
<script src="/js/v1/event_off_360.js?v=201705311"></script>
@endpush