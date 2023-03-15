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

$( document ).ready(function() {
    setVolumeDefault(0.5);
 });

function setVolumeDefault(volume)
{
	var vid = document.getElementById("video");
	if(vid != null)
	{
		 vid.volume = volume;
	}
};
function findUsOn(social)
{
	//ga('send', 'event', 'Buttons', 'click', 'Find us on '+social);
	//return false;
};
function downloadVideo()
{
	//ga('send', 'event', 'Buttons', 'click', 'Download video');
	//return false;
};

var getXsrfToken = function() {
    var cookies = document.cookie.split(';');
    var token = '';

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].split('=');
        if(cookie[0] == 'XSRF-TOKEN') {
            token = decodeURIComponent(cookie[1]);
        }
    }

    return token;
};

var show_msg = function(msg, id) {
    $("#"+id).html(msg);
    $("#"+id).addClass("show");

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ $("#"+id).removeClass("show"); }, 5000);
};

var generate_twitter_follow = function(username){
    return "https://twitter.com/intent/user?screen_name=" + username;
};

var generate_facebook_follow = function(username){
    return "https://www.facebook.com/" + username;
};

var generate_reddit_follow = function(username){
    return "https://www.reddit.com/user/" + username;
};

var show_delete = function(el){
    if (el.parent().hasClass('show')){
        el.parent().removeClass('show');
    }
    else{
        el.parent().addClass('show');
    }
};
//# sourceMappingURL=main-header.js.map
