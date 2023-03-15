@extends('layouts.master')
@section('title', 'Customize your Boom Meter')
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
        <div class="boom_meter_title_s">Customize your Boom Meter
            <div class="link_name"><span class="boom_meter_fun">Note:</span> You have to re-add BrowseSource plugin in OBS to update the change.</div>
        </div>


        <div class="boom_meter_list_s">
            <div class="custom">
                <div>
                    {!! csrf_field() !!}
                    <input type="hidden" value="{{$code}}" id="codeUser">
                    <label class="label_css" for="cssCustom">Custom css (auto run demo)</label><br/> <br/>
                    <textarea id="cssCustom" rows="26" cols="60">{{$cssContent}}</textarea>
                    <br/>
                </div>
                <div class="btn-action">
                    <button class="boom_meter_button" onclick="window.location='{{URL::previous()}}';">Back</button>
                    <button class="boom_meter_button" onclick="reviewCss();">Review</button>
                    <button class="boom_meter_button" id= "btn-demo" onclick="demo();">Demo</button>
                    <button class="boom_meter_button" onclick="uploadCss();">Save</button>
                </div>
            </div>
            <div id="textDisplay">
                <img src="{{$path}}1.gif{{$version}}" id="main-image"/>
                @if($hasZero)
                <img src="{{$path}}0.gif{{$version}}" class="main-image" style="display: none;" />
                @endif
            </div>
        </div>

    </main>
    <style type="text/css" id="styleCustom">
        {{$cssContent}}
    </style>
    @if($hasZero)
    <style type="text/css" class="styleCustom">
        {{$cssContentForZero}}
    </style>
    @endif
@endsection
@push("content-javascript")
<script type="text/javascript">
    var start = 0;
    @if($hasZero)
    start = 0;
    @endif
    var back_url = '{{URL::previous()}}';
    var path = "{{$path}}";
    var version = "{{$version}}";
    console.log(version);
    var images = new Array();
    var maxImgNum = 9;
    for (i = start; i <= maxImgNum; i++) {
        images[i] = new Image();
        images[i].src = path+'' + i + '.gif'+version;
    }
    var k = 1;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDRising.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDStatic.png'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CD5half.gif'+version;
    var status = 1;
</script>
<script type="text/javascript">
    $(document).ready(function(){
        demo();
    });
    function showZero()
    {

    }
    function reviewCss()
    {
        var cssContent = $("#cssCustom").val();
            $('#styleCustom').html(cssContent);
        var cssContentClass  = cssContent.replace("#", ".");
            cssContentClass  = cssContentClass.replace("}", "z-index: 999; }");
            $('.styleCustom').html(cssContentClass);
    }
    function uploadCss()
    {
        var url = "{{url('')}}";
        $.post(url+"/upload-css-boom-meter",
            {
                content:$("#cssCustom").val(),
                code:$("#codeUser").val(),
                _token:$("input[name=_token]").val(),
            },
            function(data){
                $('#styleCustom').html($("#cssCustom").val());
                show_msg(data.message,"snackbar");
                //window.location = back_url;
            }
        );
    }
    function demo()
    {
        $("#btn-demo").prop("disabled",true);
        var handTimeout;
        for(var i = 1; i <= images.length; i++) {
            var time = 1000;
            (function(i){
                handTimeout = setTimeout(function(){
                    var srcImage = images[i].src;
                    showImage(srcImage);
                    if(i == images.length - 1)
                    {
                        $("#btn-demo").prop("disabled",false);
                    }
                }, time * i)
            })(i);
        }
        clearTimeout(handTimeout);
    }
    function showImage(srcImage)
    {
        console.log("show img " + srcImage);
        var img = document.getElementById('main-image');
        if (img.src !== srcImage) {
            img.src = srcImage;
        }
    }
</script>
@endpush
