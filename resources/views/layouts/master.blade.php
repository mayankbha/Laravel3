<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="title" content="@yield('title')">
        <meta  name="description" content="@yield('description')"/>
        <meta  name="hostname" content="boom.tv"/>
        <meta  property="og:site_name" content="BOOM.TV"/>
        <meta  property="og:url" content="@yield('ogurl')"/>
        <meta  property="og:type" content="@yield('ogtype')"/>
        <meta  property="og:title" content="@yield('title')"/>
        <meta  property="og:description" content="@yield('description')"/>
        <meta  property="og:image" content="@yield('ogimage')"/>
        <meta  property="og:video" content="@yield('ogvideo')"/>
        <meta  property="og:video:width" content="1280"/>
        <meta  property="og:video:height" content="720"/>
        <meta  property="og:video:type" content="video/mp4"/>
        <!-- <meta  property="og:video:secure_url" content="https://"/> -->
        <meta  name="twitter:site" content="@boomtv3d"/>
        <meta  name="twitter:url" content="@yield('ogurl')"/>
        <meta  name="twitter:title" content="@yield('title')"/>
        <meta  name="twitter:description" content="@yield('description')"/>
        <meta  name="twitter:image" content="@yield('ogimage')"/>
        <meta name="twitter:image:src" content="@yield('ogimage')">
        <meta  name="twitter:image:alt" content="@yield('title')"/>
        <meta  name="twitter:card" content="player"/>
        <meta  name="twitter:player" content="@yield('embed_player')"/>
        <meta  name="twitter:player:width" content="1280"/>
        <meta  name="twitter:player:height" content="720"/>
        <meta  name="twitter:player:stream" content="@yield('ogvideo')"/>
        <meta  name="twitter:player:stream:content_type" content="video/mp4"/>
	    <meta http-equiv="Content-Security-Policy" >
        {{--<meta  name="apple-itunes-app" content="app-id=1112546921, app-argument=oddshottv://shot/UzrWZRGnWZRJoZrkIZfpW5Ov"/> -->
        <meta  name="al:ios:url" content="oddshottv://shot/UzrWZRGnWZRJoZrkIZfpW5Ov"/>
        <meta  name="al:android:url" content="oddshottv://shot/UzrWZRGnWZRJoZrkIZfpW5Ov"/>--}}
        <title>BOOM.TV - @yield('title')</title>
        <link rel="stylesheet" media="all" type="text/css" href="{{config('content.cloudfront') . elixir('css/rel/main-app.css') }}" >
        {{--{!! Html::style(config('content.cloudfront').'/css/'.config('content.css_ver').'/reset.css') !!}
        {!! Html::style(config('content.cloudfront').'/css/'.config('content.css_ver').'/base.css') !!}
        {!! Html::style(config('content.cloudfront').'/css/'.config('content.css_ver').'/popup.css') !!}
        {!! Html::style(config('content.cloudfront').'/css/'.config('content.css_ver').'/app.css') !!}--}}
        <link rel="stylesheet" media="all" type="text/css" href="{{config('content.cloudfront') . elixir('css/slick/main.css') }}" >
        {{--{!! Html::style(config('content.cloudfront').'/slick/slick.css') !!}
        {!! Html::style(config('content.cloudfront').'/slick/slick-theme.css') !!}
        {!! Html::style(config('content.cloudfront').'/slick/boomTvCustom.css') !!}--}}
       <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div id="snackbar"></div>
        @include('layouts.header')
        @yield('content')
        @include('layouts.footer')
        <script type="text/javascript">
            var url= "{{url('/')}}";
            var urlContent = "{{config('aws.cloudfront_content')}}";
            var urlAccess = "{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/'}}";
            var urlFilter = "{{route('filter')}}";
            var FILTER_NONE = 0;
            var FILTER_CAROUSEL = 1;
            var FILTER_TRENDING = 2;
            var FILTER_RECENT = 3;
            var FILTER_HIGHLIGHTS = 4;
            var FILTER_VIDEO360 = 5;
            var FILTER_GAME = 6;
            var FILTER_USER = 7;
            var IMG_FILTER_NONE = 0;
            var IMG_FILTER_STREAMER = 1;
            var IMG_FILTER_CHANNEL = 2;
            var DEFAULT_NUMBER_SLICK = 12;
            var urlImageFilter = "{{route('filterImage')}}";
        </script>
        {{--{!! Html::script(config('content.cloudfront').'/js/'.config('content.js_ver').'/navigation.js') !!}
        {!! Html::script(config('content.cloudfront').'/js/'.config('content.js_ver').'/dropdown.js') !!}
        {!! Html::script(config('content.cloudfront').'/js/'.config('content.js_ver').'/stats-options.js') !!}
        {!! Html::script(config('content.cloudfront').'/js/'.config('content.js_ver').'/popout.js') !!}
        {!! Html::script('/js/timezone.js') !!}
        {!! Html::script('/js/popup.js') !!}--}}
        {!! Html::script(config('content.cloudfront').'/js/vendor-js/jquery.js') !!}
        <script type="text/javascript" src="{{config('content.cloudfront') . elixir('js/rel/main-header.js')}}"></script>
        <script type="text/javascript" src="{{config('content.cloudfront') . elixir('js/rel/main-footer.js')}}"></script>
        @stack('content-javascript')
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

          //ga('create', 'UA-88936630-1', 'auto');
          ga('create', "{{config('video.trackingId')}}", 'auto');
          ga('send', 'pageview');

        </script>
        @stack('ga-javascript')
    </body>
</html>
