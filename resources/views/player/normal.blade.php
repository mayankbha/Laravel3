@extends('player.player')
@section('linkDownload', $link)
@section('popup')
	<div id="video_hls">
		@if($job_status!=true)
			<div id="video_loading" class="video_loading">
				<img class="img_loading" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/loading.gif'}}">
				<p>We are processing the video. Please wait a moment...</p>
			</div>
			<script type="text/javascript">
						var hls_data="<input id=\"streamURL\" class=\"innerControls\" type=\"hidden\" value=\"{{$linkHls}}\"/><video autoplay controls width=\"100%\" height=\"auto\" poster=\"{{$poster}}\" src=\"{{$link}}\" id=\"video\"></video><br><canvas style=\"visibility: hidden;\" id=\"buffered_c\" height=\"15\" class=\"videoCentered\" onclick=\"buffered_seek(event);\"></canvas>";
						hls_data+="<script type=\"text\/javascript\" src=\"{{url('/hls_js/hls.min.js')}}\"><\/script>";
						hls_data+="<script type=\"text\/javascript\" src=\"{{url('/hls_js/canvas.js')}}\"><\/script>";
						hls_data+="<script type=\"text\/javascript\" src=\"{{url('/hls_js/metrics.js')}}\"><\/script>";
						hls_data+="<script type=\"text\/javascript\" src=\"{{url('/hls_js/jsonpack.js')}}\"><\/script>";
						hls_data+="<script type=\"text\/javascript\" src=\"{{url('/hls_js/comom.js')}}\"><\/script>";
			</script>
			<div id="video_hls_data"></div>
		@else
			<div id="video_hls_data">
				<input id="streamURL" class="innerControls" type="hidden" value="{{$link}}"/>
				<video autoplay controls width="100%" height="auto" poster="{{$poster}}" src="{{$link}}" id="video"></video>
				<br>
				<canvas style="visibility: hidden;" id="buffered_c" height="15" class="videoCentered" onclick="buffered_seek(event);"></canvas>
			</div>
		@endif
		</div>
@endsection	
@section('script')
	{!! Html::script('/js/video_normal.js') !!}
	{!! Html::script('/js/popup.js') !!}
	{!! Html::script('/js/sync_video.js') !!}
	{!! Html::script('/js/check_video.js') !!}
	{!! Html::script('/js/popup.js') !!}
	{!! Html::script('/js/sync_video.js') !!}
	@if($job_status!=true)
		<script type="text/javascript">
			$( document ).ready(function() { var like_state = 1;checkVideo()});
		</script>
	@else
		<script type="text/javascript">
			$( document ).ready(function() {VideoInit()});
		</script>	
	@endif
@endsection	
