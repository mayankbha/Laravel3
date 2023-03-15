@extends('layouts.master')
@section('title', 'Download Boom App')
@section('ogtype', 'article')
@section('ogurl', route('download'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main class="download">
        <div class="gradient">
            <div class="background"></div>
        </div>
        <div class="download_title">Get Boom Meter</div>
        <div class="download_subtitle">Show instant replays LIVE on your stream</div>
        <div class="download_subtitle2">For Twitch and Mixer</div>
        <div class="download_video">
            <div id="landing_player"></div>
        </div>
        <a class="download_btn" href="{{$boom_setting->get('boom_app_download_link')->value}}" target="_blank">
            <img src="/assets/v1/download-win.png">
            Download Now
        </a>
        <div class="download_about">
            <div class="download_about_section">
                <img class="download_about_icon" src="/assets/v1/dw-grow.png">
                <div class="download_about_text">Grow your subscriber base</div>
            </div>
            <div class="download_about_section">
                <img class="download_about_icon" src="/assets/v1/dw-extend.png">
                <div class="download_about_text">Extend the best moments from your stream</div>
            </div>
            <div class="download_about_section">
                <img class="download_about_icon" src="/assets/v1/dw-used.png">
                <div class="download_about_text">Used by top Twitch and Mixer streamers</div>
            </div>
        </div>
    </main>
@endsection
@push('content-javascript')
<script type="text/javascript">
    // 2. This code loads the IFrame Player API code asynchronously.
    var tag = document.createElement('script');
    var landing_player;

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // 3. This function creates an <iframe> (and YouTube player)
    //    after the API code downloads.
    function onYouTubeIframeAPIReady() {
        yt_player_width = $('.download_video').width();
        yt_player_height = yt_player_width * (9/16);
        landing_player = new YT.Player('landing_player', {
            playerVars: {rel: 0},
            width : yt_player_width,
            height : yt_player_height,
            videoId: '72iQ5MjIppw',
            autoplay : 1,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    };
    function onPlayerReady(event) {
        event.target.playVideo();
    }
    function onPlayerStateChange(event) {

    };
    $(window).resize(function(){
        yt_player_width = $('.download_video').width();
        yt_player_height = yt_player_width * (9/16);
        $('#landing_player').css({width : yt_player_width,height : yt_player_height});
    });
</script>
@endpush
@push('ga-javascript')
<script type="text/javascript">
    $('.download_btn').click(function (event) {
        event.preventDefault();
        ga('send', 'event', 'DOWNLOAD_BOOM_APPS', 'download', 'In download page');
        window.open($(this).attr('href'));
    });
</script>
@endpush
