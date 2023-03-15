<html>
<head>
    {!! Html::script('/js/socket.io.min.1.7.2.js') !!}
    <script type="text/javascript">
    var total = 0;
    // preload images
    var socket;
    var WS_EVENT_ID_CMD = 'boom:command';
    var CMD_JOINCHANNELLISTEN = 1009;
    var RPL_REPLAYREQNOMINATED = 2001; 
    var RPL_REPLAYREQFAILED = 2002;
    var RPL_REPLAYREQSUCCESS = 2003;
    var RPL_REPLAYREQNUMCHANGED = 2004;
    var url = "{{config('socket.url')}}";
    var chanel = "{{$chanel}}";
    var path = "{{URL::to('/') }}/boom_status/webm/";
    var interVal = null;

    var videos = new Array();
    var max = 9;
    for (i = 0; i< max; i++) {
        videos[i] = document.createElement('video');
        videos[i].src = path+'' + i + '.webm';
    }

     function WebSocketTest()
     {
        if(chanel != "")
        {
         socket = io(url);
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
             changeStatus("A new replay is nominated! Total : " + total);
             showStage(1);
             break;
         case RPL_REPLAYREQFAILED:
             changeStatus("Replay request failed because not enough request!");
             updateStatus("0");
             break;
         case RPL_REPLAYREQSUCCESS:
             changeStatus("Replay request success! A new replay is generated!");
             updateStatus("9");
             break;
         case RPL_REPLAYREQNUMCHANGED:
             total = data.args[1];
             changeStatus("Number of request changed!!!" + data.args[0] + "-- total : "+ data.args[1]);
             showStage(data.args[0]);
             break;
         default: 
             changeStatus("Unknown command from server!");
             break;
         }
     }

    function showStage(numberRequest)
    {
        var seg = Math.floor(total/4);
        if(numberRequest == 1 || numberRequest > 1 && numberRequest <= seg)
        {
            stayStage(numberRequest, 1, 1, 2);
        }
        if(numberRequest > seg && numberRequest <= 2*seg)
        {
            stayStage(numberRequest, seg+1, 3, 4);
        }
        if(numberRequest > 2*seg && numberRequest <= 3*seg)
        {
            stayStage(numberRequest, 2*seg+1, 5, 6);
        }
        if(numberRequest > 3*seg && numberRequest < total)
        {
            stayStage(numberRequest, 3*seg+1, 7, 8);
        }
    }
    function stayStage(numberRequest, condition, start, end)
    {
        var intervalHand;
        if(numberRequest == condition)
        {
            console.log("start" + start + "--->" + end);
            updateImage(start);
            var i=0
            intervalHand = setInterval(function(){
                i++; 
                if(i > 0)
                 {
                    clearInterval(intervalHand);
                    console.log("stays " + end);
                    updateImage(end);
                 }
            }, 3000);
        }
        else
        {
            console.log("stays "+end);
            updateImage(end);
        }
    }
     function changeStatus(newStatus) {
         /*document.getElementById("textDisplay").innerHTML = document.getElementById("textDisplay").innerHTML + "<br/>" + newStatus;*/
         console.log(newStatus);
     } 

     
     function showImage() {
         console.log("show image!");
         //interVal = setInterval(updateImage, 1000);
         var img = document.createElement("img");
         img.id = 'main-image';
         document.getElementById('textDisplay').appendChild(img);
     }
 
     function updateImage(count) {
         /*document.getElementById("textDisplay").innerHTML = '' + count;*/
         var img = document.getElementById('main-image');
         img.src = path+'' + count + '.webm';
         if (count >= 9)  count = 0;
     }

     function updateStatus(status)
     {
        var srcimg = path+status+".webm";
             var img = document.getElementById('main-image');
             img.src = srcimg;
             var i=0
             var intervalHand = setInterval(function(){
                i++; 
                updateImage(0);
                if(i > 0)
                 {
                    clearInterval(intervalHand);
                 }
            }, 3000);
     }
    </script>
    <style type="text/css">
        #main-image {
          position: fixed; 
          top: 0; 
          left: 0; 
            
          /* Preserve aspet ratio */
          width: 100%;
          height: auto;
        }
    </style>
<body onload="WebSocketTest();">
<div id="textDisplay">
<video id="main-image" controls autoplay loop>
  <source src="{{URL::to('/') }}/boom_status/webm/0.webm" type="video/webm">
</video>
</div>
</body>
</html>

