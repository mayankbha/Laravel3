<div class="card_image" id="card_image">
		<input type="hidden" value="{{config('aws.sourceLink').$video->link_hls}}" name="link" id="link"/>
        <input type="hidden" value="{{config('aws.sourceLink').$video->thumbnail}}" name="poster" id="poster"/>
		<ul class="card_play">
			<li class="play_circle one"></li>
			<li class="play_circle two"></li>
			<li class="play_triangle"></li>
		</ul>
</div>

@push('content-javascript')

{!! Html::script(config('content.cloudfront') . '/jwplayer/'.config('content.jwplayer_ver').'/jwplayer.js') !!}
{!! Html::script('/js/player/speedjw.js') !!}
{!! Html::script('/js/player/next-button.js?v=201705291') !!}
{!! Html::script('/js/player/jwplayer.js?v=201706081') !!}
@endpush

