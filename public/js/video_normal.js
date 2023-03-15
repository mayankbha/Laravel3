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
function resizeVideo() {
 
  var w =$("#video").width();
  var h =$("#video").height();

  $("#overlay").height(h-40);
  $("#overlay_wrap").height(h-40);
  $(".frame_image").height(h-40);

}
function share(item) {
    var link=$(item).attr('href');
    var popup= window.open(link,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    var pollTimer = window.setInterval(function() {
        if (popup.closed !== false) { // !== is required for compatibility with Opera
            window.clearInterval(pollTimer);
            getShare();
        }
    }, 200);
}
function play(){
    var vid = document.getElementById("video");
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
function setPlayState(){
  if(playing_status==true)
  {
     $("#pause_icon").addClass("pause");
     $("#pause_icon").removeClass("play_triangle");
  }
  else
  {
    $("#pause_icon").addClass("play_triangle");
    $("#pause_icon").removeClass("pause");
  }
  
}
function likeVideo(){
     $.post(url+"/likevideo",
      {
          vcode:vcode
      },
     function(data){
        $('#like_numb').html(data);
        like_state=!like_state;
        setLikeState();
     }
     );
}
function getShare(){
     $.post(url+"/getshare",
      {
          vcode:vcode
      },
     function(data){
        $('#share_numb').html(data);
      
     }
     );
}
function setLikeState() {
   if(like_state==1)
    {
       
        $("#liked").addClass("show");
        $( "#not_like" ).addClass("hide");

        $("#liked").removeClass("hide");
        $( "#not_like" ).removeClass("show");
        $("#btn_like").attr("title", "Unlike this video");
    }
    else
    {
        $("#liked").addClass("hide");
        $( "#not_like" ).addClass("show");

        $("#liked").removeClass("show");
        $( "#not_like" ).removeClass("hide");
        $("#btn_like").attr("title", "Like this video");
    }   
}


$( document ).ready(function() {


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
    VideoInit();

});

function VideoInit(){

$( document ).ready(function() {

  
  var vid = document.getElementById("video");
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
                    document.getElementById('video').setAttribute('poster',urlContent+"/assets/v1/bg_video.png");
                    vid.autoplay=false;
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