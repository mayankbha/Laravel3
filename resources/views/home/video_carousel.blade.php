@foreach($videos as $key=> $video)  
			<div class="card_big">
					<a class="card_image" href="{{route('playvideo').'?v='.$video->code}}">
						<img src="{{config('aws.sourceLink').$video->thumbnail}}">
						<ul class="card_play">
							<li class="play_circle one"></li>
							<li class="play_circle two"></li>
							<li class="play_triangle"></li>
						</ul>
					</a>
					<div class="card_properties">
						<div class="card_profile">
							<div class="card_name">
								<a class="name_icon" href="#">
									@if($video->user()->first()->avatar!=null)
				                    <img src="{{$video->user()->first()->avatar}}">
				                    @else
				                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
				                    @endif
								</a>
								<a class="name_context video-title-nowrap" href="#">{{$video->user()->first()->displayname}}</a>
							</div>
							<a class="card_type video-title-nowrap" href="#" title="{{$video->getGameNames()}}">{{$video->getGameNames()}}</a>
						</div>
						<div class="card_info">
							<div class="card_socials">
								<a onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?original_referer={{$video->link}}&ref_src=twsrc%5Etfw&amp;text=&tw_p=tweetbutton&amp;url={{$video->link}}">
								<img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"> </a>
								<!-- <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/reddit-big.png'}}"> -->
								<a onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?u={{$video->link}}&src=sdkpreparse" title="Share on Facebook">
								<img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
								</a>
							</div>
							<div class="card_statistics">
								<div class="card_likes">
									<img onclick="likeVideo();" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
								<span id="like_numb">{{$video->like_numb}} </span>
								</div>
								<div class="card_views">
									<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
									<span id="view_numb">{{$video->view_numb}} </span>
								</div>
							</div>
						</div>
					</div>
				</div>
@endforeach
