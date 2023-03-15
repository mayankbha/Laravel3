@extends('layouts.master')
@section('title', 'Sign up for the Slick Daddy Club Private VR beta')
@section('ogtype', 'article')
@section('ogurl', route('vrbeta'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <div class="background_big">
        <div class="gradient_big"></div>
    </div>
    <!-- <div class="coming_soon_nav">
			<a class="coming_soon_logo" href="{{route('landing')}}">
				<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/logo.png'}}">
			</a>
			<a class="coming_soon_contact" href="{{url('/contact')}}">
				<div class="contact_icon">
					<img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/contact.png'}}">
				</div>
				Contact
			</a>
		</div> -->
    <div class="wrap_cooming_soon">
        <div class="cooming_soon_context">
            <div class="cooming_soon_title">Sign up for the Slick Daddy Club Private VR beta</div>
        </div>
        <div id="subscribe_input" class="subscribe_input" style="top: 80%">

            <div class="inputholder">
                <input id="email" type="text" placeholder="Email">
            </div>
            <div class="inputholder">
                <input id="twitch_id" type="text" placeholder="Twitch ID">
            </div>
            <a id="contact_btn" class="subscribe_button" href="#" onclick="validateEmail()">
                Register
                <div class="subscribe_button_shadow">
                    <div class="subscribe_button_shadow_two"></div>
                </div>
            </a>
            <div id="subscribe_error" class="subscribe_error">That email or twitch id is invalid!</div>

        </div>
        <div id="subscribe_thanks" class="subscribe_thanks"></div>
    </div>
    <style>
        .cooming_soon_context{
            margin: 0 auto;
        }
    </style>
@endsection
@push('content-javascript')
<script src="{{url('/js/vrbeta.js')}}"></script>
@endpush
