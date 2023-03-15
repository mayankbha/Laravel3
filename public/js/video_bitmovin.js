function syncState(){
  var position=player.getCurrentTime();
  player.seek(position);
}
function moveVideoSmall() {
  $("#video-360").appendTo("#video_location");
  //jwplayer("video-360").resize("auto","340");
  syncState();
   
}
function moveVideoPopup() {
  $("#video-360").appendTo("#video_popup");
  //jwplayer("video-360").resize("100%","auto");
  syncState();
   
}
function setTurnPopup ()
{
    $(".btn_fullscreen_jw").css("right",20);
    $(".btn_fullscreen_jw").css("bottom",10);
} 
function VideoInit() {
  
$( document ).ready(function() {
    setTurnPopup();
    getActionInfo();
/*    console.log(localStorage.qualityCurr);
    if (typeof(localStorage.qualityCurr) !== "undefined") {
        player.setVideoQuality(localStorage.qualityCurr);
    }
    player.addEventHandler(bitmovin.player.EVENT.ON_VIDEO_PLAYBACK_QUALITY_CHANGED, function(e){
               var data = player.getPlaybackVideoData();
                console.log(data.id);
                localStorage.setItem("qualityCurr", data.id);
    });
*/
     // inc view 
      var time=0;
      var duration = player.getDuration();
      
      if(duration>vtime)
          time = Math.round(duration*3/10)*1000;
      else
          time = Math.round(duration)*1000;

      setTimeout(function(){
          $.post(url+"/incview",
              {
                  vcode:vcode
              },
           function(data){
              $('#view_numb').html(data);
           }
           );
      }, time);
      //end
      //next video
     if(vnext!="")
     {
       player.addEventHandler(bitmovin.player.EVENT.ON_PLAYBACK_FINISHED, function(e){
            setTimeout(function(){
                if(player.hasEnded())
                {
                    var link=url+"/w?v="+vnext;
                    window.location.assign(link);
                }
            },5000);
        });
        
     }
   
     //end
});
}
