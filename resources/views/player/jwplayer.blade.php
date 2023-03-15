@extends('player.player')
@section('linkDownload', $link)
@section('linkPopup', $link)
@section('popup')
    @if($type == 2) // video 360
	    	<div id="video-360">
		
		        <input type="hidden" value="{{$link360['2048']}}" name="link" id="link"/>
		        <input type="hidden" value="{{$poster}}" name="poster" id="poster"/>

			</div>
    @else
    		<div id="video-360">
				@if($job_status!=true)
				<div id="video_loading" class="video_loading">
					<img class="img_loading" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/loading.gif'}}">
					<p>We are processing the video. Please wait a moment...</p>
				</div>
				       
		        @endif
		        <input type="hidden" value="{{$linkHls}}" name="link" id="link"/>
		        <input type="hidden" value="{{$poster}}" name="poster" id="poster"/>
			</div>
	@endif
@endsection	
@section('script')
	{!! Html::script('/jwplayer/jwplayer.js') !!}

	<!-- {!! Html::script('/js/controller_360.js') !!} -->
	<script type="text/javascript">
	    jwplayer.key="2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
	   	var link = $("#link").val();
	   	var poster = $("#poster").val();
	 </script>
	@if($type == 2) // video 360
	<script type="text/javascript">
	  	jwplayer("video-360").setup({
		    "file": link,
		    "image": poster,
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
	 <script type="text/javascript">
	 		jwplayer("video-360").setup({
			    "file": link,
			    "image": poster,
			    "autostart": true,
			   	"aspectratio": "16:9",
				"width": "100%",
			  });
	 </script>
	 @endif
	{!! Html::script('/js/video_jw.js') !!}
	{!! Html::script('/js/popup.js') !!}
	{!! Html::script('/js/check_video.js') !!}
	<script type="text/javascript">
		$( document ).ready(function() {VideoInit()});
	</script>
@endsection	
