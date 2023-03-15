var RATIO = 16/9;
function likeVideo()
{
     $.post(url+"/likevideo",
      {
          vcode:vcode
      },
     function(data){
        $('#like_numb').html(data);
     }
     );
};
function countView(duration, vtime)
{
  var time=0;
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
};
