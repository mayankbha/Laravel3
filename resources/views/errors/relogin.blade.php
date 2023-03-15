
@extends('layouts.master')
@section('title','Login Errors' )

@section('content')
    @if(isset($redirect_uri))
        @php($login_url = route('oauth',['is_claim'=>1,'source'=>0,'redirect_uri'=>$redirect_uri]))
    @else
        @php($login_url = route('oauth',['is_claim'=>1,'source'=>0]))
    @endif
    <main>
        <h1 style="color: #FFF; text-align: center; margin-top: 100px; font-size: 18px;">Uh oh, something went wrong. Please try  <a style="color: #f08c30;" href="{{$login_url}}" data-toggle="list-login">logging</a> in to your Twitch account again.
      </h1>
    </main>
    <div style="height: 500px;"></div>
  
@endsection