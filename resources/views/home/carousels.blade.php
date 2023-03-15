@foreach($videos as $key=> $video)
	@php ($video_user = $video->user()->first())
	<div class="card_big">
					<a class="card_image" href="{{route('playvideo').'?v='.$video->code}}">
					@if($video->type == 2)
		            <label class="icon_360"></label>
		            @endif
						<img src="{{config('aws.sourceLink').$video->thumbnail}}" onError='this.onerror=null;this.src="{{$imageDefault}}";' >
						<ul class="card_play">
							<li class="play_circle one"></li>
							<li class="play_circle two"></li>
							<li class="play_triangle"></li>
						</ul>
					</a>
					<div class="card_properties">
						<div class="card_profile">
							<div class="card_name">
								<a class="name_icon" href="{{route('profile',[$video_user->name])}}">
									@if($video_user->avatar!=null)
				                    <img src="{{$video_user->avatar}}">
				                    @else
				                    <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}">
				                    @endif
								</a>
								<a class="name_context video-title-nowrap" href="{{route('profile',[$video_user->name])}}">{{$video_user->displayname}}</a>
							</div>
							<a class="card_type video-title-nowrap" href="#" title="{{$video->getGameNames()}}">{{$video->getGameNames()}}</a>
						</div>
						<div class="card_info">
							<div class="card_socials">
								<a class="card_social" onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?hashtags=boomtv&original_referer={{$video->link}}&ref_src=twsrc%5Etfw&amp;text={{$video->getTextShare()}}&tw_p=tweetbutton&amp;url={{$video->link}}">
								<img  src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-big.png'}}"> </a>
								<!-- <img class="card_social" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/reddit-big.png'}}"> -->
								<a class="card_social" onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?quote={{$video->getTextShare()}}   %23boomtv&u={{$video->link}}&src=sdkpreparse&hashtag=%23boomtv" title="Share on Facebook">
								<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-big.png'}}">
								</a>
								@if($video_user->type == $video_user::USER_TYPE_TWITCH)
								<a  class="card_social" target="_blank" href="{{$video_user->getProfileLink()}}" title="Go to Twitch stream">
									<img  src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/twitch-profile.png'}}">
								</a>
								@else
								<a  class="card_social" target="_blank" href="{{$video_user->getProfileLink()}}" title="Go to Mixer stream">
									<img  src="{{config('content.cloudfront').'/build/assets/'.config('content.assets_ver').'/mixer-icon.png'}}">
								</a>
								@endif
							</div>
							<div class="card_statistics">
								<div class="card_likes" data-vcode="{{$video->code}}">
									<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
								<span id="like_numb">{{$video->like_numb}} </span>
								</div>
								<div class="card_views">
									<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/view.png'}}">
									<span id="view_numb">{{$video->getViewNumbSort()}} </span>
								</div>
							</div>
						</div>
					</div>
				</div>
@endforeach
