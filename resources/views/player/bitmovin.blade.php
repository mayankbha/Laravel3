@extends('player.player')
@section('linkDownload', $link)
@section('linkPopup', $link)
@section('popup')
    		<div id="video-360">
				<input type="hidden" value="" name="link" id="link"/>
		        <input type="hidden" value="{{$poster}}" name="poster" id="poster"/>
			</div>
@endsection
	
@section('script')
<!-- <script type="text/javascript" src="https://bitmovin-a.akamaihd.net/bitmovin-player/stable/7/bitmovinplayer.js"></script> -->
{!! Html::script('/bitmoviplayer_js/bitmovinplayer.js') !!}
<script type="text/javascript">
var poster = $("#poster").val();
var divplay = "video-360";
var type = "{{$type}}";
var link_hls= "{{$linkHls}}";
console.log("link hls" + link_hls);
var player = typeof bitmovin !== "undefined" ? bitmovin.player(divplay) : bitdash(divplay);;
	 var conf = {
        key: '0319f92c-6e38-4e73-9336-64089683bb58',
        source: {
          hls: "{{$linkHls}}",
         // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
          poster: poster,
//          vr: {startupMode: '2d',startPosition: 180}
        },
        style: {aspectratio: '16:9'},
        playback : { autoplay: true}

        };
	console.log(type);
	if(type==2) {
	conf = {
	key: '0319f92c-6e38-4e73-9336-64089683bb58',
	source: {
	  hls: link_hls,
	 // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
	  poster: poster,
	  vr: {startupMode: '2d',startPosition: 180}
	},
	style: {aspectratio: '16:9'},
	playback : { autoplay: true}

	};
        }
	player.setup(conf).then(function(value) {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                player.setVideoQuality('480_1786000');
                if(window.innerHeight < window.innerWidth)
                {       
                        player.enterFullscreen();
                }
        }
        console.log('Successfully created bitdash player instance');
        $(".bmpui-ui-watermark").hide();
        }, function(reason) {
        console.log('Error while creating bitdash player instance');
        });

       $(window).on('orientationchange', function(event) {
                if(orientation == 90)
                {
                 player.enterFullscreen();
                }
        }); 
	
</script>

{!! Html::script('/js/player.js') !!}
{!! Html::script('/js/video_bitmovin.js') !!}
{!! Html::script('/js/popup.js') !!}
{!! Html::script('/js/check_video.js') !!}
<script type="text/javascript">
		$( document ).ready(function() {VideoInit();});
</script>
@endsection	
