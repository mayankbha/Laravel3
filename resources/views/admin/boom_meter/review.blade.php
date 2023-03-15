<html>
<head>
    {!! Html::script('/js/vendor-js/jquery.js') !!}
    <script type="text/javascript">
    var path = "{{$path}}";
    var version = "{{$version}}";
    console.log(version);
    var images = new Array();
    var maxImgNum = 9;
    for (i = 1; i <= maxImgNum; i++) {
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
    </script>
    <style type="text/css" id="styleCustom">
        {{$cssContent}}
    </style>
    @if($hasZero)
    <style type="text/css" class="styleCustom">
        {{$cssContentForZero}}
    </style>
    @endif
<body>
<div id="textDisplay">
<img src="{{$path}}1.gif{{$version}}" id="main-image"/>
@if($hasZero)
<img src="{{$path}}0.gif{{$version}}" class="main-image"/>
@endif
</div>
<div class="custom">
    <div>
    {!! csrf_field() !!}
    <input type="hidden" value="{{$code}}" id="codeUser">
    <label for="cssCustom">Custom css (auto run demo)</label><br/> <br/>
    <textarea id="cssCustom" rows="26" cols="60">{{$cssContent}}</textarea>
     <br/>
    </div>
    <div class="btn-action">
        <button><a href="{{ URL::previous() }}">Back</a></button>
        <button onclick="reviewCss();">Review</button>
        <button id= "btn-demo" onclick="demo();">Demo</button>
        <button onclick="uploadCss();">Save</button>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    demo();
});
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
    var active = 1;
    $.post(url+"/afkvr-admin/uploadCss",
                {
                    content:$("#cssCustom").val(),
                    code:$("#codeUser").val(),
                    _token:$("input[name=_token]").val(),
                    active:active
                },
             function(data){
                alert(data.message)
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
</body>
</html>

