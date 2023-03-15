@extends('event.layout')
@section('title', $event->title)
@section('ogtype', 'video')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main class="events empty">
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
                    <div class="event_livestream">
                        <a class="livestream_play" href="#">
                            <img src="/assets/v1/events-play.png">
                        </a>

                        <div class="livestream_title">{{$event->offline_title}}</div>
                        <div class="livestream_about">{{$event->offline_description}}</div>
                        <div class="livestream_share_options">
                            <a class="livestream_share">
                                <img src="/assets/v1/events-share.png">
                                Share the event with your friends
                            </a>
                            <div class="livestream_share_dropdown">
                                <a class="livestream_share_option" target="_blank" href="{{\App\Helpers\Helper::generate_facebook_share_link()}}"><img src="/assets/v1/fb-2.png"> Share on Facebook</a>
                                <a class="livestream_share_option" target="_blank" href="{{\App\Helpers\Helper::generate_twitter_share_link()}}"><img src="/assets/v1/twitter-2.png"> Share on Twitter</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="events_context">
                    <div class="ad_unit">
                        <img class="ad_logo" src="/assets/v1/ad-logo.png">
                        <div class="ad_context">{{$event->event_description}}</div>
                    </div>
                </div>
            </div>
        </div>
        @include('event.inside_footer')
    </main>
@endsection
@push('content-javascript')
<script type="text/javascript">
    var shareBtn = document.getElementsByClassName('livestream_share')[0];
    var shareOptions = document.getElementsByClassName('livestream_share_options')[0];

    window.addEventListener('click', function (e) {
        if (typeof shareBtn != "undefined") {
            if (shareBtn.contains(e.target)) {
                shareOptions.className = "livestream_share_options show";
            } else {
                shareOptions.className = "livestream_share_options";
            }
        }
    });
    check_event_online = function(){
        var request = $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
            },
            url: "/event/state",
            method: "POST",
            data: {
            }
        });
        request.done(function (data) {
            if (data.status == 1){
                location.reload();
            }
        });

        request.fail(function (jqXHR, textStatus) {
            show_msg(textStatus, snackbar);
        });
    }

    $(document).ready(function () {
        setInterval(function(){
            check_event_online();
        },10000);
    })
</script>
@endpush