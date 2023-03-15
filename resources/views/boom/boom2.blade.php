<html>
<head>
    {!! Html::script('/js/vendor-js/jquery.js') !!}
    <script type="text/javascript">
    var path = "{{$path}}";
    var version = "{{$version}}";
    console.log(version);
    var images = new Array();
    var maxImgNum = 0;
    var k = 1;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'1.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'3.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'5.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'8.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'9.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDRising.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDStatic.png'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'1.gif'+version;
    k++;
    </script>
    <style type="text/css">
        {{$cssContent}}
    </style>
<body>
<div id="textDisplay">
<img src="{{$path}}1.gif{{$version}}" id="main-image"/>
</div>
<div class="custom">
    <div>
    {!! csrf_field() !!}
    </div>
    <div class="btn-action">
        <button id= "btn-demo" onclick="demo();">Demo</button>
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
    var active = $('#active').prop('checked');
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

