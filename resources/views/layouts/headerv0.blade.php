<nav>
<a class="nav_logo" href="{{route('landing')}}">
	<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo.png'}}">
</a>
<div class="nav_links">
	<a class="nav_link" href="{{route('home')}}">
		<div class="link_image">
			<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/videos.png'}}">
		</div>
		<div class="link_name">Videos</div>
	</a>
	<a class="nav_link" href="{{url('/signup')}}">
		<div class="link_image">
			<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/downloads.png'}}">
		</div>
		<div class="link_name">Download</div>
	</a>
	<a class="nav_link" href="{{url('/about')}}">
		<div class="link_image">
			<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/about.png'}}">
		</div>
		<div class="link_name">About</div>
	</a>
</div>
</nav>