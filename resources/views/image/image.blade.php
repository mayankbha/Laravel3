<div class="card">
	<a class="card_image" href="{{route('image').'?i='.$image->code}}">
		<img src="{{$sourceLink.$image->paths3}}">
	</a>
	<ul class="card_properties">
		@if($image->user)
		<a class="card_type video-title-nowrap" href="#" title="">{{$image->user->name}}</a>
		@else
			<a class="card_type video-title-nowrap" href="#" title="">#</a>
		@endif
	</ul>
	<div class="card_stats_new">
		<div class="card_statistics">
			<div class="card_likes_new" data-vcode="{{$image->code}}">
				<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
				<span id="like_numb">{{$image->like_numb}} </span>
			</div>
			<div class="card_views_new">
				<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
				<span id="view_numb">{{$image->view_numb}} </span>
			</div>
		</div>
	</div>
</div>
