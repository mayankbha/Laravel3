
$( document ).ready(function() {
     
      var time=0;
      var duration =jwplayer("video").getDuration();
      
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
              console.log("Inc View To "+data);
           }
           );
      }, time);
      //end

});