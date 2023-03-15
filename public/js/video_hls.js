 var vid = document.getElementById("video");
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


function moveVideoSmall() {
  $("#video_hls").appendTo("#video_location");
  resizeVideo(); 
  vid.play(); 
   
}
function moveVideoPopup() {
  $("#video_hls").appendTo("#video_popup");
  vid.play(); 
}


var playing_status=true;
var offset=1;

function resizeVideo() {
 
  var w =$("#video").width();
  var h =$("#video").height();

  $("#overlay").height(h-40);
  $("#overlay_wrap").height(h-40);
  $(".frame_image").height(h-40);

}

function play(){
   
    if(playing_status==true)
    {
        vid.pause(); 
        playing_status=false;
       

    }
    else
    {
        vid.play(); 
        playing_status=true;
        
    }
    setPlayState()

}


$( document ).ready(function() {

  
     resizeVideo();
    $( window ).resize(function() {
      resizeVideo();
    });

  

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
    

});

function VideoInit(){

$( document ).ready(function() {

        vid = document.getElementById("video");
  
        vid.onloadeddata =function() {
             resizeVideo();
            // inc view 
            var time=0;
            if(vid.duration>vtime)
                time = Math.round(vid.duration*3/10)*1000;
            else
                time = Math.round(vid.duration)*1000;

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
            vid.addEventListener('ended',nextVideo,false);
            function nextVideo(e) {

                playing_status=false;
                setPlayState();
                if(vnext!=""){
                    // $("#video_wrap").hide();
                    // document.getElementById('video').setAttribute('poster',urlContent+"/assets/v1/bg_video.png");
                    // vid.autoplay=false;
                    // vid.load();
                    $('#vname_next').show();
                    setTimeout(function(){
                        if(playing_status===false)
                        {
                            var link=url+"/w?v="+vnext;
                            window.location.assign(link);
                        }
                       
                    },5000);
                }
               
            
                 
            }
            
        }
         $('#video').on('play', function (e) {
            playing_status=true;
            $('#vname_next').hide();
            $("#video_wrap").show();
        });
         //end


});
}