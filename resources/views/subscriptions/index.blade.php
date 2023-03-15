@extends('layouts.master')
@section('title', 'Subscriptions')
@section('ogtype', 'notifications')
@section('ogurl', route('subscriptions'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')

<main>
    <div class="gradient">
        <div class="background"></div>
    </div>
    
<!-- NOTIFICATIONS START -->
    <div class="notifications">
        @if(Session::has('message'))
            <div class="notifications_line main {{ Session::get('alert-class') }}">
                  {{ Session::get('message') }}
            </div>
        @endif
        
        <div class="notifications_tittle">Subscribed ({{sizeof($subscribed)}})</div>
        @if($subscribed && sizeof($subscribed)>0)
            @foreach($subscribed as $key=> $val)   
                <div class="notifications_line main subscribed_streamer_{{$val->streamer->id}}">
                    <div class="notifications_user">
                        <div class="notifications_user_icon">
                            <img src="{{$val->streamer->avatar}}">
                        </div>
                        <div class="notifications_user_name">{{$val->streamer->name}}</div>
                    </div>
                    <div class="">
                        <label class="switch"> 
                            <input type="checkbox" checked onclick="Unsubscribe({{$val->streamer->id}})" name="{{$val->streamer->name}}" />
                            <div class="slider round"></div>
                        </label>
                    </div>
                </div>
            @endforeach
        @else 
            <div class="notifications_line">
                <div class="notifications_user">
                    <div class="notifications_user_name">No subscribed streamer found!</div>
                </div>
            </div>
        @endif

        <div class="notifications_tittle">Recommended ({{sizeof($recommended)}})</div>
        @if($recommended && sizeof($recommended)>0)
            @foreach($recommended as $key=> $val)
                <div class="notifications_line">
                    <div class="notifications_user">
                        <div class="notifications_user_icon">
                            <img src="{{$val['avatar']}}">
                        </div>
                        <div class="notifications_user_name">{{$val['name']}} ( {{$val['followers']}} )</div>
                    </div>
                    <div class="">
                        <label class="switch"> 
                            <input type="checkbox" onclick="Subscribe({{$val['id']}})" name="{{$val['name']}}" />
                            <div class="slider round"></div>
                        </label>
                    </div>
                </div>
            @endforeach
        @else 
            <div class="notifications_line">
                <div class="notifications_user">
                    <div class="notifications_user_name">No recommendations available at this time!</div>
                </div>
            </div>
        @endif
    </div>
<!-- NOTIFICATIONS ENDING -->
</main>

<script type="text/javascript">
function Unsubscribe(id){    
    var request = $.ajax({
        url: "/subscriptions/unsubscribe",
        method: "GET",
        data: {
            streamer_id:id
        }
    });
    request.done(function (data) {
        if (data.status == 1){
            location.reload();
        }
    });

    request.fail(function (jqXHR, textStatus) {
        show_msg(textStatus, snackbar);
    });
}
function Subscribe(id){
    var request = $.ajax({
        url: "/subscriptions/subscribe",
        method: "GET",
        data: {
            streamer_id:id
        }
    });
    request.done(function (data) {
        if (data.status == 1){
            location.reload();
        }
    });

    request.fail(function (jqXHR, textStatus) {
        show_msg(textStatus, snackbar);
    });
}
</script>

@endsection    
