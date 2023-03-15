<div class="card_image" id="card_image">
		<video autoplay controls width="100%"  poster="{{config('aws.sourceLink').$video->thumbnail}}" src="{{config('aws.sourceLink').$video->links3}}" id="video">
		</video>
		<ul class="card_play">
			<li class="play_circle one"></li>
			<li class="play_circle two"></li>
			<li class="play_triangle"></li>
		</ul>
</div>
@push("content-javascript")
{!! Html::script('/js/player/next-button.js?v=201705171') !!}
{!! Html::script('/js/player/video_normal.js') !!}
@endpush