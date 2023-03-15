@extends('layouts.master')
@section('title', 'Boom Game Images')
@section('description', 'Boom Game Images')
@section('ogtype', 'article')
@if(!$isDetail)
@section('ogurl', route('image'))
@else
@section('ogurl', route('image')."?i=".$image->code)
@endif
@section('ogimage',($image==null)?(config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png'):($sourceLink.$image->paths3))
@section('ogvideo','')
@section('embed_player','')
@section('content')
	<main>
		<div class="gradient">
            <div class="background"></div>
        </div>
        @if($isDetail)
            @include("image.detail",["image" => $image])
        @endif
        @foreach($channels as $key => $channel)
            <div class="carousel_title">{{$channel->name}}</div>
            <div class="variable-width channel-category"
                 data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
                 data-channel = "{{$channel->name}}"
                 id="image-channel_{{$channel->id}}">
            </div>
        @endforeach
	</main>
@endsection
@push('content-javascript')
{!! Html::script(config('content.cloudfront').'/slick/slick.js') !!}
{!! Html::script(config('content.cloudfront').'/slick/slick.min.js') !!}
{!! Html::script('/js/image/slick-responsive.js') !!}
{!! Html::script('/js/image/init.js') !!}
@endpush


