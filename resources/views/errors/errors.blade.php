
@extends('layouts.master')
@section('title','Content Not Found' )

@section('content')
 <main>
    <h1 style="color: #FFF; text-align: center; margin-top: 100px;">An error occurred, <a style="color: orange;" href="{{route('oauth')}}" data-toggle="list-login">back</a>
  </h1>
</main>
<div style="height: 500px;"></div>
@endsection