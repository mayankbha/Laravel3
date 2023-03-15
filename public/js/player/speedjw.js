var trident = !!navigator.userAgent.match(/Trident\/7.0/);
var net = !!navigator.userAgent.match(/.NET4.0E/);
var IE11 = trident && net
var IEold = ( navigator.userAgent.match(/MSIE/i) ? true : false );
var theVideo;
var player;
function setPlayer(playerCurrent)
{
	player = playerCurrent;
}
function setSpeed(e, speed)
{
	$(e).parent().find(".jw-option").removeClass("jw-active-option");
	$(e).addClass("jw-active-option");
	theVideo = document.querySelector('video');
	theVideo.defaultPlaybackRate = speed;
	theVideo.playbackRate = speed;
	if (player.getRenderingMode() == "flash"){
		return;
	}
	if(IE11 || IEold)
	{
			player.seek(player.getPosition());
			player.onSeek(function(){theVideo.playbackRate = speed;});
			player.onPause(function(){theVideo.playbackRate = speed;});
			player.onPlay(function(){theVideo.playbackRate = speed;});
			theVideo.playbackRate = speed;
	} else {
		player.seek(player.getPosition());
		theVideo.playbackRate = speed;
	}
}
