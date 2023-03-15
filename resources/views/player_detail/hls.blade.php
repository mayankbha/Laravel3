<div class="card_image" id="card_image">
<input id="streamURL" class="innerControls" type="hidden" value="{{config('aws.sourceLink').$video->link_hls}}"/>
		<video autoplay controls width="100%"  poster="{{config('aws.sourceLink').$video->thumbnail}}" src="" id="video"></video>
		<br>
		<canvas style="visibility: hidden;" id="buffered_c" height="15" class="videoCentered" onclick="buffered_seek(event);"></canvas>
		<ul class="card_play">
			<li class="play_circle one"></li>
			<li class="play_circle two"></li>
			<li class="play_triangle"></li>
		</ul>
</div>

{!! Html::script('/hls_js/hls.min.js') !!}
{!! Html::script('/hls_js/canvas.js') !!}
{!! Html::script('/hls_js/metrics.js') !!}
{!! Html::script('/hls_js/jsonpack.js') !!}
{!! Html::script('/hls_js/comom.js') !!}
{!! Html::script('/js/player/video_normal.js') !!}