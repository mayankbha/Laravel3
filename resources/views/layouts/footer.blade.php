<footer class="browse_footer">
	<div class="footer_container">
		<div class="footer_about">
			<div class="about_us">
				<a class="about_logo" href="{{url('/')}}">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-v2.png'}}">
				</a>
				<div class="about_info">Copyright © 2016, BoomTV</div>
			</div>
			<div class="about_links">
				<a class="about_link" href="{{url('/terms')}}">Terms and Conditions</a>
				<a class="about_link" href="{{url('/privacy')}}">Privacy</a>
				<a class="about_link" href="{{url('/dmca')}}">DMCA</a>
				<a class="about_link" href="https://angel.co/boomtv">Jobs</a>
				<a class="about_link" href="{{url('/faq')}}">FAQ</a>
				<a class="about_link" href="{{url('/about')}}">About</a>
				<a class="about_link" href="{{url('/contact')}}">Contact</a>
				<!-- <a class="about_link" href="{{url('/about')}}">VR Ready</a> -->
			</div>
		</div>
		<div class="footer_social">
			<div class="social_title">Find us on</div>
			<div class="social_links">
				<a onclick="findUsOn('linkedin');" class="social_link" href="https://www.linkedin.com/company/boom.tv">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/in.png'}}">
				</a>
				<a onclick="findUsOn('facebook');" class="social_link" href="https://www.facebook.com/boomtv/
">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/fb-2.png'}}">
				</a>
				<a onclick="findUsOn('twitter');" class="social_link" href="https://twitter.com/boomtv3d">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitter-2.png'}}">
				</a>
			</div>
		</div>
	</div>
</footer>