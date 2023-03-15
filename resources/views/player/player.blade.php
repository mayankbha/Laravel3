@extends('layouts.master')
@if($video_info->type==3)
	@section('title', $video_info->title.' - '.$author->displayname)
@else
	@section('title', $video_info->getGameNames().' - '.$author->displayname)
@endif
@section('ogtype', 'video.other')
@section('ogurl', $video_info->link)
@section('ogimage', $poster)
@section('ogvideo', $link)
@section('description', "BOOM.TV - Boom Game Videos")

@section('embed_player', route('embed',$video_info->code))
@section('content')
		<main>
			<div class="gradient">
				<div class="background"></div>
			</div>
			<header>
				<div class="video">

					<!-- Trigger/Open The Modal -->
					
					<button id="myBtn" class="btn_fullscreen_jw"></button>
					<!-- <div class="controller_360"><div class="keypad_360">
						<a class="triangle-up" href="#" onclick="keyUp();"></a> 
						<a class="triangle-left" href="#" onclick="keyLeft();"></a> 
						<a class="triangle-right" href="#" onclick="keyRight();"></a> 
						<a class="triangle-down" href="#" onclick="keyDown();"></a> 
					</div></div> -->
					<div class="vnext">
						<p id="vname_next" hidden>
							Next video <br>
							<b>{{$vname_next}}</b>
						</p>
						</div>
					<div id="video_location">
						
					</div>
				</div>
				<div class="video_info">
					<ul class="video_properties">
						<li class="video_user">
							<a class="user_icon" href="#">
								@if($author->avatar!=null)
								<img src="{{$author->avatar}}"/>
								@else
								<img id="no-ava" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/icon-1.png'}}"/>
								@endif
							</a>
							<a class="user_name" href="#">{{$author->displayname}}</a>
						</li>
						@if($video_info->type==3)
							<a href="#" class="video_type" title="{{$video_info->title}}">
							<span class="video_title">
							@foreach($video_info->getTitle() as $key => $value)
								
								{{$value}}<br>
							
							@endforeach

							</span></a><br>
						@else
							<br>
						@endif
						<a class="video_type" href="#" title="{{$video_info->getGameNames()}}">{{$video_info->getGameNames()}}</a>
						<li class="video_date">{{$video_info->formatTime($request)}}</li>
						@if($video_info->type==2)
						<li class="vr_type">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/360.png'}}">
						</li>
						@endif
					</ul>
					<ul class="video_feedback">
						<li class="video_share">
							<div class="video_shares">
								<div class="shares_title">Shares</div>
								<div class="shares_number" id="share_numb">{{$video_info->share_numb}}</div>
							</div>
							<div class="video_shareto">
								<div class="shareto_title">Share to</div>
								<div class="shares_options">
									<a id="btn_share" class="shares_option" onclick="share(this);return false;" href="https://www.facebook.com/sharer/sharer.php?u={{$video_info->link}}&src=sdkpreparse" title="Share on Facebook">
										<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb.png'}}">
									</a>
									<a class="shares_option" onclick="share(this);return false;" title="Share on Twitter" href="https://twitter.com/intent/tweet?original_referer={{$video_info->link}}&ref_src=twsrc%5Etfw&amp;text=&tw_p=tweetbutton&amp;url={{$video_info->link}}">
										<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter.png'}}">
									</a>
								</div>
							</div>
							
						</li>
						<li class="video_response">
							<div class="video_likes">
								<div class="likes_title">Likes</div>
								<div class="likes_stats">
							
									<a id="btn_like" class="likes_icon" href="#" onclick="likeVideo();">
										<img id="liked" class="liked" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/liked.png'}}">
										<img id="not_like" class="not_liked" src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/like.png'}}">
									</a>
							
									<div class="likes_number" id="like_numb"></div>
								</div>
							</div>
							<div class="video_views">
								<div class="views_title">Views</div>
								<div class="views_number" id="view_numb"></div>
							</div>
						</li>
					</ul>
				</div>
			</header>

			<div class="related">
				<div class="related_title">Related videos</div>
				<ul class="related_cards" id="related_list">
				@include('home.list_video')
				
				</ul>
				@if($total > 12)
                    <a class="load_more" onclick="loadVideo()">Load more</a>
                    <input type="hidden" value="12" name="page" id="page"/>
        		@endif
			</div>
		</main>


	<!-- The Modal -->
	<div id="myModal" class="modal">
		<a onclick="downloadVideo()" class="download" href="@yield('linkDownload')">Download</a>
	  <!-- Modal content -->
	  <div class="modal-content">
	    <span class="close"></span>
	    	<div id="video_popup">
	    		@yield('popup')
	    	</div>						
			
	  </div>
	</div>
	<script type="text/javascript">
		var vcode= "{{$vcode}}";
		var vtime= "{{$vtime}}";
		var vnext= "{{$vnext}}";
		var like_state={{$like_state}};		
	</script>
  	@yield('script')

@endsection		
