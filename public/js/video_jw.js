
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];


// When the user clicks on the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
    moveVideoPopup();
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
    moveVideoSmall();
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        moveVideoSmall();
    }
}


function syncState(){
  jwplayer().pause(true)
  var position=jwplayer().getPosition();
 // jwplayer().stop();
  jwplayer().seek(position);
   
 
}
function moveVideoSmall() {
  $("#video-360").appendTo("#video_location");
  jwplayer("video-360").resize("auto","340");
  syncState();
   
}
function moveVideoPopup() {
  $("#video-360").appendTo("#video_popup");
  jwplayer("video-360").resize("100%","auto");
  syncState();
   
}

function VideoInit() {
  
$( document ).ready(function() {
     setLikeState();
     $.post(url+"/getview",
            {
                vcode:vcode
            },
         function(data){
            $('#view_numb').html(data);

         }
     );
     //end
        
    //get like count
     $.post(url+"/getlike",
        {
            vcode:vcode
        },
        function(data){
            $('#like_numb').html(data);

        }
     );
     //end
     // inc view 
      var time=0;
      var duration =jwplayer("video-360").getDuration();
      
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
         jwplayer("video-360").onComplete(function(){
            console.log(vnext);
           setTimeout(function(){
                if(jwplayer("video-360").getState()=="complete")
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