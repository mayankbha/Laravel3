@extends('layouts.master')
@section('title', 'About')
@section('ogtype', 'article')
@section('ogurl', route('about'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
<main>
			<div class="gradient">
				<div class="background"></div>
			</div>
			<div class="about_header">
				<div class="about_header_title">Boom is the first 3D live-streaming platform for watching eSports on any device</div>
				<!-- <div class="about_header_info">Instant replays and cinematic camera angles on web & mobile. In VR, it’s like you’re there.</div> -->
				
			</div>
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
			<ul class="tiles">
				<li class="tile">
					<div class="tile_title">
						<span>1</span>
						Why
					</div>
					<div class="tile_about">Hundreds of millions of fans watch eSports, but are held captive to the player’s view. When we watch traditional sports our experience of the game is augmented with different camera angles, replays, slow-motion, and other live editing techniques we don’t even notice anymore. We believe eSports deserve the same.</div>
				</li>
				<li class="tile">
					<div class="tile_title">
						<span>2</span>
						How
					</div>
					<div class="tile_about">Game streamers download a simple application that allows Boom.tv to construct a true 3D experience from their local stream in real time. Streamers just play, Boom.tv does the rest. Viewers can experience this gameplay in 3D on any 2D device or in full VR using the headset of their choice.</div>
				</li>
				<li class="tile">
					<div class="tile_title">
						<span>3</span>
						Vision
					</div>
					<div class="tile_about">We believe the experience of watching eSports entertainment should be far more exciting, social, and interactive than even traditional sports broadcasts. Our mission is to put 3D technology in the hands of millions of gamers and revolutionize the way eSports content is produced and consumed.</div>
				</li>
			</ul>
			<div class="team">
				<div class="team_title">The team behind the BoomTV</div>
				<ul class="team_cards">
					<li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-sg.jpg'}}">
						</div>
						<div class="team_card_name">Sumit Gupta</div>
						<div class="team_card_status">CEO</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/sugupta">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://www.linkedin.com/in/guptasumit">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li>
					<li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-hn.jpg'}}">
						</div>
						<div class="team_card_name">Ha Viet Nguyen</div>
						<div class="team_card_status">CTO</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/afkvr_jp">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://www.linkedin.com/in/ha-nguyen-0959572a">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li>
					<li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-ct.jpg'}}">
						</div>
						<div class="team_card_name">Christian Talmage</div>
						<div class="team_card_status">Creative Director</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/BrveNewWrld">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://www.linkedin.com/in/christiantalmage">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li>
					<li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-gb.jpg'}}">
						</div>
						<div class="team_card_name">Guy Beahm</div>
						<div class="team_card_status">Advisor</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/GuyBeahm">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://www.linkedin.com/in/guy-beahm-84a5b72b">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li>
					<!-- <li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-jh.jpg'}}">
						</div>
						<div class="team_card_name">Jens Hilgers</div>
						<div class="team_card_status">Advisor</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/JensHilgers">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://de.linkedin.com/in/jenshilgers">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li> -->
					<li class="team_card">
						<div class="team_card_icon">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/profile-av.jpg'}}">
						</div>
						<div class="team_card_name">Avinash Dabir</div>
						<div class="team_card_status">VP Marketing</div>
						<div class="team_card_links">
							<a class="team_card_link" href="https://twitter.com/bringmedabir">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-tw.png'}}">
							</a>
							<a class="team_card_link" href="https://www.linkedin.com/in/adabir/">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/team-li.png'}}">
							</a>
						</div>
					</li>
				</ul>
				<div class="team_about">With a diverse team of 30 passionate engineers, designers & product specialists from the worlds of computer vision, 3D graphics, gaming, media and VR/AR. Together we are <img class="logo_about" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo.png'}}"></div>
			</div>
			<div class="investors">
				<div class="investors_title">Our Investors</div>
				<ul class="investors_cards">
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-1.png'}}">
					</li>
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-2.png'}}">
					</li>
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-3.png'}}">
					</li>
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-4.png'}}">
					</li>
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-5.png'}}">
					</li>
					<li class="investors_card">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-investor-8.png'}}">
					</li>
				</ul>
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
