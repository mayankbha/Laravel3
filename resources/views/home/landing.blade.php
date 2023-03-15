@extends('layouts.master')
@section('title', 'Homepage')
@section('ogtype', 'article')
@section('ogurl', route('landing'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <header class="landing">
        <div class="background_big">
            <div class="gradient_big"></div>
        </div>
        <div class="header_title">The First 3D Live-Streaming Platform for eSports</div>
        <div class="header_info">Players just play, Boom.tv does the rest</div>
        <div class="video_introduce">
            <a class="header_thumbnail" href="#" onclick="about_page_popshow()">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/thumbnail_landing.jpg'}}">
            </a>
            <a class="header_popout_holder" href="#" onclick="about_page_pophide()">
                <div class="header_popout_container">
                    <iframe id="landing_player" class="header_popout" width="560" height="315"
                            src="https://www.youtube.com/embed/3avhNzGYq5k?version=3&enablejsapi=1"
                            frameborder="0" allowfullscreen>
                    </iframe>
                </div>
            </a>
        </div>
        <ul class="headlines">
            <li class="headline">
                <a href="" class="headline_about">Try it now</a>
                <div class="headline_title">Download the Boom App for Streamers</div>
                <div class="headline_buttons">

                    <a href="{{url('/signup')}}" class="headline_button_2">
                        <div class="headline_btn_icon">
                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/win.png'}}">
                        </div>
                        Win
                    </a>
                <!-- <a href="#" class="headline_button_2">
							<div class="headline_btn_icon">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/mac.png'}}">
							</div>
							Mac
						</a> -->
                </div>

            </li>
            <li class="headline">
                <a href="#" class="headline_about">Explore and share</a>
                <div class="headline_title">Watch your favorite streamers in action
                </div>
                <a href="{{url('/home/popular')}}" class="headline_button">Browse videos</a>
            </li>
            <li class="headline">
                <a href="#" class="headline_about">Coming soon</a>
                <div class="headline_title">Sneak peak into amazing VR experiences
                </div>


                <a href="{{url('/about')}}" class="headline_button">Learn more</a>
            </li>
        </ul>
    </header>
    <main class="landing">
        <div class="features">
            <div class="features_title">Features and technology</div>
            <ul class="features_cardholder">
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/cursor.png'}}">
                    </div>
                    <div class="card_about">Makes gameplay more exciting for fans with real-time instant replays.</div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/feature.png'}}">
                    </div>
                    <div class="card_about">Extend the reach of your broadcasts with easily shareable video clips and
                        highlight reels.
                    </div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/record.png'}}">
                    </div>
                    <div class="card_about">Now for the first time cinematic camera angles offer a new standard in
                        production quality
                    </div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/integration.png'}}">
                    </div>
                    <div class="card_about">Seamless integration with industry standard broadcast tools like OBS and
                        XSplit
                    </div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/click.png'}}">
                    </div>
                    <div class="card_about">One click montage of the day’s replays makes it simple to create stunning
                        highlights
                    </div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/command.png'}}">
                    </div>
                    <div class="card_about">Streamers control when and where they capture replays, all without
                        interrupting their game
                    </div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/crossplatform.png'}}">
                    </div>
                    <div class="card_about">Cross platform 3D live-streaming via web, mobile and virtual reality</div>
                </li>
                <li class="features_card">
                    <div class="card_image">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/present.png'}}">
                    </div>
                    <div class="card_about">Unlocks opportunities for new revenue streams with sponsored replays and ad
                        placement
                    </div>
                </li>
            </ul>
        </div>
        <div class="opinions">

            <ul class="opinions_streamers">
                <li class="streamer selected">
                    <a class="streamer_image" href="javascript:void(0)" onclick="changeQuote(1)">
                        <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/streamer.png'}}">
                    </a>

                </li>
            <!-- <li class="streamer">
						<a class="streamer_image" href="javascript:void(0)" onclick="changeQuote(2)">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
						</a>
						<a class="streamer_name" href="javascript:void(0)" onclick="changeQuote(2)">NightSkyPlayer</a>
					</li>
					<li class="streamer">
						<a class="streamer_image" href="javascript:void(0)" onclick="changeQuote(3)">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-1.png'}}">
						</a>
						<a class="streamer_name" href="javascript:void(0)" onclick="changeQuote(3)">AlexIwoi</a>
					</li> -->
            <!-- <li class="streamer" style="visibility: hidden;">
						<a class="streamer_image" href="javascript:void(0)" onclick="changeQuote(4)">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-1.png'}}">
						</a>
						<a class="streamer_name" href="javascript:void(0)" onclick="changeQuote(4)">SirBenSkye</a>
					</li> -->
            </ul>
            <ul class="opinions_contextholder">
                <li class="opinions_context">
                    <div class="opinions_title">What Streamers think of us</div>
                    <div class="opinions_about">The Boom App gives me access to an incredible streaming toolset that's
                        beneficial for both my community and the World Champion himself. This is the future.
                        <a class="streamer_name" href="https://www.twitch.tv/drdisrespectlive">Dr. Disrespect</a>
                    </div>

                </li>
                <!-- <li class="opinions_context">
                    <div class="opinions_title"> Streamer 2 Title</div>
                    <div class="opinions_about">The BoomTV app offers instant replays and multiple viewing angles for viewers by constructing a true 3D experience. I honestly think this is a gamechanger and will recomment it to my gaming friends.</div>
                </li>
                <li class="opinions_context">
                    <div class="opinions_title"> Streamer 3 Title</div>
                    <div class="opinions_about">The BoomTV app offers instant replays and multiple viewing angles for viewers by constructing a true 3D experience. I honestly think this is a gamechanger and will recomment it to my gaming friends.</div>
                </li>
                <li class="opinions_context">
                    <div class="opinions_title"> Streamer 4 Title</div>
                    <div class="opinions_about">The BoomTV app offers instant replays and multiple viewing angles for viewers by constructing a true 3D experience. I honestly think this is a gamechanger and will recomment it to my gaming friends. The BoomTV app offers instant replays and multiple viewing angles for viewers by constructing a true 3D experience. </div>
                </li> -->
            </ul>

        </div>
        <div class="videos">
            <div class="videos_title">Featured Videos</div>
            <!-- <div class="videos_about">See some of the best examples though our curated videos</div> -->
            <div class="videoholder">
                @foreach($tops as $video)
                    <a class="video featured_card" href="{{route('playvideo').'?v='.$video->code}}">

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
                @endforeach
                <a class="arrow left" onclick="sliderLeft()" href="javascript:void(0)">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/left-arrow.png'}}">
                </a>
                <a class="arrow right" onclick="sliderRight()" href="javascript:void(0)">
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/right-arrow.png'}}">
                </a>
            </div>
        </div>
    </main>

    <script>
        // 2. This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');
        var landing_player;

        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // 3. This function creates an <iframe> (and YouTube player)
        //    after the API code downloads.
        function onYouTubeIframeAPIReady() {
            landing_player = new YT.Player('landing_player', {
                playerVars: {rel: 0},
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
            //event.target.playVideo();
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        // var done = false;
        // function onPlayerStateChange(event) {
        //   if (event.data == YT.PlayerState.PLAYING && !done) {
        //     setTimeout(stopVideo, 6000);
        //     done = true;
        //   }
        // }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                about_page_endvideo();
            }
        }
        function stopVideo() {
            player.stopVideo();
        }

        var popoutBox = document.getElementsByClassName('header_popout_holder')[0];

        function about_page_popshow() {
            landing_player.playVideo();
            ga('send', 'event', 'Buttons', 'click', 'Play introduced video');
            popoutBox.style.cssText = "opacity: 1; z-index: 1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s 0s;";
            document.onkeydown = function(evt) {
                evt = evt || window.event;
                if (evt.keyCode == 27) {
                    about_page_pophide();
                }
            };

        }
        function about_page_endvideo() {
            popoutBox.style.cssText = "opacity: 0; z-index: -1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s .5s;";
        }
        function about_page_pophide() {
            popoutBox.style.cssText = "opacity: 0; z-index: -1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s .5s;";
            landing_player.pauseVideo();
        }
    </script>
@endsection
