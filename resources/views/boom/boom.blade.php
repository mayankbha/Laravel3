<html>
<head>
    {!! Html::script('/js/vendor-js/jquery.js') !!}
    {!! Html::script('/js/socket.io.min.1.7.2.js') !!}
    <script type="text/javascript">
    var total = 0;
    var count = 0;
    var threshold = 0;
    var thresholdTime = 0;
    var currentImgNum = 1;
    var timeCounter = 0;
    var cooldownTime = 0;
    var changeOldTimefix = 3000;
    var changeOldTime = changeOldTimefix;
    var changeRisingTimefix = 35000;
    var changeRisingTime = changeRisingTimefix;
    var risingTime = 1000;
    var cd5halfTime = 5000;
    var playingOldTime = 0;
    var startTimeMS = 0;

    // preload images
    var socket;
    var WS_EVENT_ID_CMD = 'boom:command';
    var CMD_JOINCHANNELLISTEN = 1009;
    var RPL_REPLAYREQNOMINATED = 2001; 
    var RPL_REPLAYREQFAILED = 2002;
    var RPL_REPLAYREQSUCCESS = 2003;
    var RPL_REPLAYREQNUMCHANGED = 2004;
    var RPL_REPLAYREQEND = 1012;
    var STATUS_NONE = 0; // no replay is nominated yet
    var STATUS_WAITING = 1; // waiting if enough request or not
    var STATUS_FINAL = 2;
    var STATUS_FAILED = 3;
    var STATUS_END = 4;
    var STATUS_TIMEOUT_FINAL = 5;
    var currentStatus = STATUS_NONE;
    var url = "{{$urlSocket}}";
    console.log("socket url: " + url);
    var chanel = "{{$chanel}}";
    var version = "{{$version}}";
    var path = "{{$path}}";
    var hasZero = "{{$hasZero}}";
    var start = 1;
    if(hasZero == 1)
    {
        start = 0;
    }
    var intervalHandle = null;
    var timeoutHandle = null;

    var images = new Array();
    var maxImgNum = 9;
    for (i = start; i <= maxImgNum; i++) {
        images[i] = new Image();
        images[i].src = path+'' + i + '.gif'+version;
    }
    var k = 1;
    /*images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'9_old.gif'+version;
    k++;*/
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CD5half.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDRising.gif'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = path+'CDStatic.png'+version;
    k++;
    images[maxImgNum+k] = new Image();
    images[maxImgNum+k].src = "{{URL::to('/')}}" + "/boom_status/EdgeOverlay.png"+version;
    function WebSocketTest()
     {
        if(chanel != "")
        {
         //socket = io(url);
          socket = io(url, {  transports: ['websocket']});
          socket.on('reconnect_attempt', () => {
               socket.io.opts.transports = ['polling', 'websocket'];
          });

         socket.on('connect', function(){
             changeStatus("Connected to bot server!");
             socket.emit(WS_EVENT_ID_CMD, {cmdId: CMD_JOINCHANNELLISTEN, args: [chanel]}, 
             function(data){
                 changeStatus("Send join channel success!");
             });
         });
         socket.on(WS_EVENT_ID_CMD, function(data){
             handleCommandFromServer(data);
             console.log(data);
         });
         socket.on('disconnect', function(){
             changeStatus("Disconnect from bot server!");
         });
        }
        else
        {
            document.getElementById("textDisplay").innerHTML = "user not found";
        }
     }

     function handleCommandFromServer(data) {
         var cmdId = data.cmdId;
         switch (cmdId) {
         case RPL_REPLAYREQNOMINATED:
             total = data.args[1];
             count = data.args[0];
             threshold = data.args[2];
             thresholdTime = data.args[3];
             cooldownTime = data.args[4];
             changeStatus("A new replay is nominated! Total : " + total + " threshold time is: " + thresholdTime + " cooldowntime: " + cooldownTime);
             currentStatus = STATUS_WAITING;
             clearCooldown();
             updateImage();
             intervalHandle = setInterval(updateImage, 1000);
             break;
         case RPL_REPLAYREQFAILED:
             changeStatus("Replay request failed because not enough request!");
            currentStatus = STATUS_FAILED;
             break;
         case RPL_REPLAYREQSUCCESS:
             changeStatus("Replay request success! A new replay is generated!");
             currentStatus = STATUS_TIMEOUT_FINAL;
             setTimeout(function() { 
                 console.log('replay request success! timeout!');
                 currentStatus = STATUS_FINAL;
             }, 2000);
             //currentStatus = STATUS_FINAL;
             break;
        case RPL_REPLAYREQEND:
            changeStatus("Replay request end!");
            currentStatus = STATUS_END;
            updateStatus(9);
            break;
         case RPL_REPLAYREQNUMCHANGED:
             changeStatus("Number of request changed!!!" + data.args[0] + "-- total : "+ data.args[1] + " threshold: " + data.args[2]);
             total = data.args[1];
             count = data.args[0];
             threshold = data.args[2];
             break;
         default: 
             changeStatus("Unknown command from server!");
             break;
         }
     }
     function reset() {
        timeCounter = 0;
        count = 0;
        total = 0;
        currentImgNum = 1;
        currentStatus = STATUS_NONE;
        clearInterval(intervalHandle);
     }

    function showStage()
    {
        changeStatus("Show stage with number of image: " + count);
        var numberRequest = count;
        /*var temp = Math.round(total / 2);*/
        var temp = threshold;
        var imgNum = 1;
        if (numberRequest >= (temp / 8) && numberRequest < (2 * temp / 8)) {
            imgNum = 2;
        } else if (numberRequest >= (2 * temp / 8) && numberRequest < (3 * temp / 8)) {
            imgNum = 3;
        } else if (numberRequest >= (3 * temp / 8) && numberRequest < (4 * temp / 8)) {
            imgNum = 4;
        } else if (numberRequest >= (4 * temp / 8) && numberRequest < (5 * temp / 8)) {
            imgNum = 5;
        } else if (numberRequest >= (5 * temp / 8) && numberRequest < (6 * temp / 8)) {
            imgNum = 6;
        } else if (numberRequest >= (6 * temp / 8) && numberRequest < (7 * temp / 8)) {
            imgNum = 7;
        } else if (numberRequest >= (7 * temp / 8) && numberRequest < (temp)) {
            imgNum = 8;
        } else if (numberRequest >= temp) {
            imgNum = 9;
        }
        
       /* if (timeCounter > (thresholdTime - 5)) {
            // check if the num of request is already past threshold
            // if that is the case, try to animate to the "red"
            var candidate = currentImgNum;
            if (count >= threshold) {
                console.log("Enough request, try to animate to target");
                if (currentImgNum < maxImgNum - 1) {
                    candidate = currentImgNum + 1;
                }
            }
            if (candidate > imgNum) imgNum = candidate;
        }*/
        showImage(imgNum);
    }

     function changeStatus(newStatus) {
         /*document.getElementById("textDisplay").innerHTML = document.getElementById("textDisplay").innerHTML + "<br/>" + newStatus;*/
         console.log(newStatus);
     } 

    function updateImage()
    {
        console.log("update image with currentStatus: " + currentStatus);
        // update every seconds
        timeCounter++;
        if (currentStatus == STATUS_WAITING)
        {
            showStage();
        }
        if(currentStatus == STATUS_FINAL || currentStatus == STATUS_END)
        {
            updateStatus(9);
        }
        if(currentStatus == STATUS_FAILED)
        {
            updateStatus(1);
        }
    }
    var setIntervalHandZero;
    var setTimoutHandZero;
    var setTimoutHandZero2
    function showImage(imgNumb) {
        var img = document.getElementById('main-image');
        var newPath = path+'' + imgNumb + '.gif'+version;
        if (img.src !== newPath) {
            changeStatus("Change image to: " + newPath);
            img.src = newPath;
            currentImgNum = imgNumb;
        } else {
            changeStatus("Same image: " + newPath);
        }
        if(hasZero == 1)
        {
            if((currentStatus == STATUS_NONE || currentStatus == STATUS_FAILED) && imgNumb == 1)
            {
                clearZero();
                console.log("has zero and show 1 gif");
                $(".main-image").show();
                setTimoutHandZero = setTimeout(function(){ $(".main-image").hide(); }, 1500);
                setIntervalHandZero = setInterval(function(){
                            console.log("show 0 gif"); 
                            $(".main-image").show();
                            setTimoutHandZero2= setTimeout(function(){ $(".main-image").hide(); }, 1500);
                }, 6000);
            }
            else
            {
                clearZero();
            }
        }
     }
     function clearZero()
     {
        console.log("hide 0 gif");
        clearInterval(setIntervalHandZero);
        $(".main-image").hide();
        clearTimeout(setTimoutHandZero);
        clearTimeout(setTimoutHandZero2);
     }
     function showImageForExtension(imgNumb, ext) {
        var img = document.getElementById('main-image');
        var newPath = path+'' + imgNumb + '.' + ext + version;
        if (img.src !== newPath) {
            changeStatus("Change image to: " + newPath);
            img.src = newPath;
            currentImgNum = imgNumb;
        } else {
            changeStatus("Same image: " + newPath);
        }
     }

    function clearCooldown()
    {
        clearTimeout(timeoutHandle);
    }
     function updateStatus(status)
     {
        if(currentStatus == STATUS_END)
        {
            clearTimeout(timeoutHandle);
            playingOldTime =  (new Date()).getTime() - startTimeMS ;
            console.log("playingOldTime: " + playingOldTime);
            // Ignore load 9 and 9 old
            changeRisingTime = 0;
            changeOldTime = 0;
            
            actionTimeout(status);
        }
        else
        {
            showImage(status);
            //remove request end status
            playingOldTime = 0;
            changeRisingTime = changeRisingTimefix;
            actionTimeout(status);
        }
        reset();
     }

    function actionTimeout(status)
    {
        startTimeMS = (new Date()).getTime();
        timeoutHandle = setTimeout(function(){
            if(status == 9)
            {
                showImage("9");
                changeOldTime = changeOldTimefix;
                console.log("startTimeMS: " + startTimeMS);
                var staticTime = cooldownTime*1000 - playingOldTime - changeOldTime -changeRisingTime - risingTime - cd5halfTime;
                console.log("staticTime: " + staticTime);
                timeoutHandle = setTimeout(function(){
                    showImage("CDRising");
                    timeoutHandle = setTimeout(function(){
                        showImageForExtension("CDStatic", "png");
                        timeoutHandle = setTimeout(function(){
                            showImage("CD5half");
                            timeoutHandle = setTimeout(function(){
                                showImage(1);
                            }, cd5halfTime);
                        }, staticTime);
                    }, risingTime);
                }, changeRisingTime);
            }
        }, changeOldTime);
    }
    </script>
    @if($checkDefault == -1)
    <style type="text/css">
        #main-image {
          position: fixed; 
          top: 25%; 
          left: 46%; 
            
          /* Preserve aspet ratio */
          width: auto;
          height: 50%;
        }

    </style> 
    @elseif($checkDefault == 0)
    <style type="text/css">
        {{$cssContent}}
        .main-image {
          position: fixed; 
          top: 24.8%; 
          left: 46%; 
            
          /* Preserve aspet ratio */
          width: auto;
          height: 50.4%;
          z-index: 10000;
        }
    </style> 
    @else
        <style type="text/css">
        {{$cssContent}}
        </style> 
        @if($hasZero)
        <style type="text/css" class="styleCustom">
            {{$cssContentForZero}}
        </style>
        @endif
    @endif
<body onload="WebSocketTest();">
<div id="textDisplay">
<img src="" id="main-image"/>
@if($hasZero)
<img src="{{$path}}0.gif{{$version}}" class="main-image" style="display: none" />
@endif
@if($useEdge == 1)
<img src="{{URL::to('/')}}/boom_status/EdgeOverlay.png{{$version}}" class="main-image"/>
@endif
</div>
<audio id="audio" controls autoplay src="" style="visibility: hidden;"/>
{!! csrf_field() !!}
<script type="text/javascript">
    var currentLocation = window.location;
    var urlCheckSession = "http://"+currentLocation.hostname + "/update-session-boom-meter";
    console.log(urlCheckSession);
    $( document ).ready(function() {
        showImage(1);
        $.post(urlCheckSession,
                {
                    userCode:"{{$userCode}}",
                    boomMeterTypeId:"{{$boomMeterTypeId}}",
                    _token:$("input[name=_token]").val(),
                },
             function(data){
                console.log(data);
             }
         );
     });
</script>
</body>
</html>

