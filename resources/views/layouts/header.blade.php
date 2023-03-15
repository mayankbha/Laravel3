<nav>
			<a class="nav_logo" href="{{route('home')}}">
				<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo-v2.png'}}">
			</a>
			<div class="nav_links">
				<!-- <div class="nav_inputholder">
					<input type="text" placeholder="Search">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/search.png'}}">
				</div> -->
				<a class="nav_link" href="{{route('videos')}}">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/videos.png'}}">
					</div>
					<div class="link_name">Videos</div>
				</a>
					@if(isset($boom_setting))
				<a class="nav_link" href="{{route($boom_setting->get('download_link_status')->value)}}">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/features.png'}}">
					</div>
					<div class="link_name">Download</div>
				</a>
					@endif
				<a class="nav_link" href="{{url('/about')}}">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/about.png'}}">
					</div>
					<div class="link_name">About</div>
				</a>
				<a class="nav_link" href="{{route('faq')}}">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/help.png'}}">
					</div>
					<div class="link_name">Help</div>
				</a>
				@if(Auth::check())
				<a class="nav_link user show" onclick="profileSettingsCustom(this)">
					<div class="link_image">
						<img src="@if(Auth::user()->avatar){{Auth::user()->avatar}}@else {{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/user-icon-1.png'}} @endif">
					</div>
					<div class="link_name">{{Auth::user()->displayname}}</div>
					<div class="link_arrow">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/arrow-bottom.png'}}">
					</div>
				</a>
				<div class="nav_dropdown">
					<a class="dropdown_link sign_out" href="{{route('logout')}}">Sign Out</a>
					<a class="dropdown_link" href="{{route('profile',['name'=>auth()->user()->name])}}">My Account</a>
					<a class="dropdown_link" href="{{route('subscriptions')}}">Subscriptions</a>
					<!-- <a class="dropdown_link" href="{{route('boom_meter')}}">Customize Boom Meter</a> -->
					{{--@if($is_admin_user)
					<a class="dropdown_link" href="{{route('login-to-afkvr-admin')}}">Login to afkvr-admin</a>
					@endif--}}
				</div>
				@else
				<!-- <a class="nav_link sign_in" href="{{\App\Helpers\Helper::createLoginUrl()}}">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-nav.png'}}">
					</div>
					<div class="link_name">Sign In</div>
				</a> -->
				<a class="nav_link user show" onclick="profileSettingsCustom(this)">
					<div class="link_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/lock.png'}}">
					</div>
					<div class="link_name">Sign In</div>
					<div class="link_arrow">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/arrow-bottom.png'}}">
					</div>
				</a>
				<div class="nav_dropdown">
					<a class="dropdown_link selector-flex " href="{{\App\Helpers\Helper::createLoginUrl()}}"> 
						<div class="link_image">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/twitch-nav.png'}}">
						</div>
						<div class="link_name link_name_mobile">Twitch</div>
					</a>
					<a class="dropdown_link selector-flex" href="{{\App\Helpers\Helper::createLoginUrl('mixer')}}">
						<div class="link_image">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/mixer-profile.png'}}">
						</div>
						<div class="link_name link_name_mobile">Mixer</div>
					</a>
					<a class="dropdown_link selector-flex " href="{{\App\Helpers\Helper::createLoginUrl('youtube')}}">
						<div class="link_image">
							<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/youtube2.png'}}">
						</div>
						<div class="link_name link_name_mobile">Youtube</div>
					</a>
				</div>
				@endif
				
			</div>
		</nav>