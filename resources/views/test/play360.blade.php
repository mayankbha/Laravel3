<html>
    <head>
{!! Html::script(config('content.cloudfront').'/js/vendor-js/jquery.js') !!}
<style type="text/css">
    body {
    	margin: 0px;
    }
	#video-jumb
	{
		position: fixed;
		top: 0px;
		left: 40%;
	}
	#video-minimap
	{
		position: fixed;
		bottom: 0px;
		left: 0px;
	}
</style>
</head>
<body>
<div class="card_image" id="card_image">
		<input type="hidden" value="http://35.161.177.235/hls360/test2_low.m3u8" name="link" id="link"/>
        <input type="hidden" value="https://s3-us-west-2.amazonaws.com/boomtv-contents/thumnail360.png" name="poster" id="poster"/>
</div>
<div id="video-jumb">
	        <input type="hidden" value="http://35.161.177.235/hls/jumbotron_low.m3u8 " name="link-jumb" id="link-jumb"/>
</div>
<div id="video-minimap">
	        <input type="hidden" value="http://35.161.177.235/hlsminimap/minimap_low.m3u8" name="link-minimap" id="link-minimap"/>
	        
</div>

{!! Html::script('/bitmoviplayer_js/bitmovinplayer.js') !!}
{!! Html::script('/jwplayer/jwplayer.js') !!}
<script type="text/javascript">
	$( document ).ready(function() {
	var divplay = "card_image"; 
	var link = $("#link").val();
	var poster = $("#poster").val();
	var player = typeof bitmovin !== "undefined" ? bitmovin.player(divplay) : bitdash(divplay);;
		conf = {
		key: '0319f92c-6e38-4e73-9336-64089683bb58',
		source: {
		  hls: link,
		 // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
		  poster: poster,
		  vr: {startupMode: '2d',startPosition: 180}
		},
		style: {aspectratio: '2:1'},
		playback : { autoplay: true}
		};
		player.setup(conf).then(function(value) {
	        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	                if(window.innerHeight < window.innerWidth)
	                {       
	                        player.enterFullscreen();
	                }
	        }
	        var duration = player.getDuration();
	        console.log(duration);
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
	});
</script>
 <script type="text/javascript">
  jwplayer.key="2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
 	var link_jumb = $("#link-jumb").val();
	var poster_jumb = $("#poster-jumb").val();
	 		jwplayer("video-jumb").setup({
			    "file": link_jumb,
			    "image": poster_jumb,
			    "autostart": true,
			   	"aspectratio": "16:9",
				"width": "400px",
			  });
	var link_minimap = $("#link-minimap").val();
	var poster_minimap = $("#poster-minimap").val();
	 		jwplayer("video-minimap").setup({
			    "file": link_minimap,
			    "image": poster_minimap,
			    "autostart": true,
			   	"aspectratio": "16:9",
				"width": "400px",
			  });
</script>
</body>
</html>
