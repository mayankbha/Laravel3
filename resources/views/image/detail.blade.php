<div class="card_big" style="opacity: 1">
    <a class="card_image" href="#">
		<img src="{{$sourceLink.$image->paths3}}">
	</a>
	<div class="card_properties">
		<div class="card_profile">
			<div class="card_name">
				<a class="name_icon" href="#">
					@if($image->user->avatar !=null)
                    <img src="{{$image->user->avatar}}">
                    @else
                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
                    @endif
				</a>
				<a class="name_context video-title-nowrap" href="{{route('profile',[$image->channel])}}">{{$image->user->name}}</a>
			</div>
			<a class="card_type" href="#">{{$image->imageChannel->name}}</a>
		</div>
		<div class="card_info">
			<div class="card_socials">
				<a onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?original_referer={{route('image').'?i='.$image->code}}&ref_src=twsrc%5Etfw&amp;text=&tw_p=tweetbutton&amp;url={{route('image').'?i='.$image->code}}">
								<img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"> </a>
				<a onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?u={{route('image').'?i='.$image->code}}&src=sdkpreparse" title="Share on Facebook">
								<img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
								</a>
			</div>
			<div class="card_statistics">
				<div class="card_likes" data-vcode="{{$image->code}}">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
					<span id="like_numb">{{$image->like_numb}} </span>
				</div>
				<div class="card_views">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
					<span id="view_numb">{{$image->view_numb}} </span>
				</div>
			</div>
		</div>
	</div>
</div>