<html>
<head>
{!! Html::script('/js/vendor-js/jquery.js') !!}

<body>
<div id="textDisplay">
    <img src="{{$path}}1.gif{{$version}}" id="main-image"/>
</div>
<script type="text/javascript">
    var back_url = '{{URL::previous()}}';
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

