<div style="width: 100%; height: auto; position: relative;" id="embed_video">
	<div style="z-index: 0;" id="video"></div>
	<a target="_blank" style="position: absolute; width: 40px; height: 40px; background: none; z-index: 1; bottom: 0; right: 0; opacity: 0;" id="btn_redirect" href="{{route('playvideo').'?v='.$video->code}}"></a>
</div>
<script type="text/javascript">
	var vtime= "{{config('video.vtime')}}";
	var url= "{{url('')}}";
	var vcode="{{$video->code}}"
</script>
{!! Html::script(config('content.cloudfront').'/js/vendor-js/jquery.js') !!}
{!! Html::script(config('content.cloudfront') . '/jwplayer/'.config('content.jwplayer_ver').'/jwplayer.js') !!}
<script src="{{url('/js/embed_player.js')}}"></script>
@if($link360!="")
	

	<script type="text/javascript">
		jwplayer.key="2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
		jwplayer("video").setup({
		    "file": "{{config('aws.cloudfront').$video->links3}}",
		    "image": "{{config('aws.cloudfront').$video->thumbnail}}",
		    "autostart": true,
		     "width": "100%",
			"aspectratio": "16:9",
		    "plugins": {
				'https://boom.tv/jwplayer/vr.js': {}
			},
			sources: [
			  {
		        file: "{{$link360['2048']}}",
		        label: "2048p",
		        "default": "true",
		      },
		      {
		        file: "{{$link360['1440']}}",
		        label: "1440p"
		      },{
		        file: "{{$link360['1080']}}",
		        label: "1080p"
		       
		      },{
		        file: "{{$link360['720']}}",
		        label: "720p",
		        
		        
		      }]
		  });
	</script>
					 
		

@else
	@if($linkHls!="")
		<script type="text/javascript">
		
		    jwplayer.key="2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
		   	jwplayer("video").setup({
			    "file": "{{$linkHls}}",
			    "image": "{{$poster}}",
			    "autostart": true,
			   	"aspectratio": "16:9",
				"width": "100%",
			  });			 		
		</script>
	@else
		<script type="text/javascript">
		
		    jwplayer.key="2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
		   	jwplayer("video").setup({
			    "file": "{{$link}}",
			    "image": "{{$poster}}",
			    "autostart": true,
			   	"aspectratio": "16:9",
				"width": "100%",
			  });			 		
		</script>
	@endif
@endif