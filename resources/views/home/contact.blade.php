@extends('layouts.master')
@section('title', 'Contact')
@section('ogtype', 'article')
@section('ogurl', route('contact'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')

<main class="contact_main">
			<div class="gradient">
				<div class="background"></div>
			</div>
			<div class="contact">
				<div class="contact_title">Get in touch</div>
				<div class="contact_about">
				<p id="contact_status"></p>
				</div>
				
				<div class="contact_inputholder">
					<div class="subscribe_input input_small">
						<div class="inputholder">
							<input type="text" id="email" placeholder="Enter your email">
						</div>
					</div>
					<div class="subscribe_input input_small">
						<div class="inputholder">
							<input type="text" id="subject" placeholder="Subject">
						</div>
					</div>
					<div class="subscribe_input input_big">
						<div class="inputholder">
							<input type="text" id="message" placeholder="Message">
						</div>
					</div>
				</div>
				<a class="contact_button" href="#" id="contact_btn">
					<div class="contact_button_image">
						<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/send.png'}}">
					</div>
					Send Email
				</a>
				
				<ul class="contact_cards">
					<br>
					<br>
					<br>
					<br>
					<br>
					<!-- <li class="contact_card map">
						<img src="{{url('theme/assets/map.png')}}">
					</li>
					<li class="contact_card">
						<div class="contact_card_title">Headquarters Address</div>
						<div class="contact_card_about">2X14 St Giles Ln<br>Mountain View, CA 94040<br>Združene države Amerike</div>
						<a class="contact_card_link" href="#">info@boom.tv</a>
						<a class="contact_card_link" href="#">01-256-813-3824</a>
					</li>
					<li class="contact_card">
						<div class="contact_card_title">Business Inquiry</div>
						<div class="contact_card_about">We are looking forward to hearing your business propositions at</div>
						<a class="contact_card_link" href="#">business@boom.tv</a>
						<a class="contact_card_link" href="#">01-256-813-3824</a>
					</li> -->
				</ul>
			</div>
		</main>
@endsection
@push('content-javascript')
<script src="{{url('/js/contact.js')}}"></script>
@endpush
