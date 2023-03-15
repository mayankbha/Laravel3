<div class="card_image" id="card_image">
		<label class="icon_360"></label>
		<input type="hidden" value="{{$linkHlsLocal}}" name="link" id="link"/>
        <input type="hidden" value="{{config('aws.sourceLink').$video->thumbnail}}" name="poster" id="poster"/>
		<ul class="card_play">
			<li class="play_circle one"></li>
			<li class="play_circle two"></li>
			<li class="play_triangle"></li>
		</ul>
</div>
@push('content-javascript')
{!! Html::script('/bitmoviplayer_js/bitmovinplayer.js') !!}
{!! Html::script('/js/player/next-button.js?v=201705171') !!}
{!! Html::script('/js/player/bitmovin.js') !!}
@endpush