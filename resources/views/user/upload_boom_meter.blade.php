@extends('layouts.master')
@section('title', 'Boom my profile')
@section('ogtype', 'article')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main>
        <div class="gradient">
            <div class="background"></div>
        </div>
        <div class="boom_meter_title">Upload image your Boom Meter</div>
        <div class="boom_meter_list">
            {{ Form::open(array('url'=>route('upload_image_boom_meter'),'files'=>true, 'class' => 'form-horizontal')) }}
            <div class="tile_about">
                <label for="images">Images zip: </label>
                <div>
                    <input type="file" class="form-control" id="images" name="file">
                </div>
            </div>
            @if($errors->any())
                <br/>
                <div class="error">
                    {{$errors->first()}}
                </div>
            @endif
            <div class="tile_about">
                <br/>
                <strong>Note: </strong> <br/>
                Files name in zip file: {{implode(", ", $images)}}<br/>
            </div>
            <br/>
            <div class="form-group">
                <div>
                    <a href="{{route('boom_meter')}}" class="boom_meter_modal_btn">Back</a>
                    {{ Form::submit('Upload and review', array('class' => 'boom_meter_modal_btn')) }}
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </main>
@endsection
