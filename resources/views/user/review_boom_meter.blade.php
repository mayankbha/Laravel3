<html>
<head>
    {!! Html::script('/js/vendor-js/jquery.js') !!}

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
        var head = $("head");
        var cssContent = $("#cssCustom").val();
        var html = '<style type="text/css">' + cssContent + '</style>';
        head.append(html);
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
                    console.log("show img " +i +"--"+ srcImage);
                    var img = document.getElementById('main-image');
                    if (img.src !== srcImage) {
                        img.src = srcImage;
                    }
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

