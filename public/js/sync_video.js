var vid = document.getElementById("video");
var vid_popup = document.getElementById("video_popup");

function syncVideo() {
	vid_popup.currentTime=vid.currentTime;
	vid_popup.play();
	vid.pause();
	playing_status=false;
	setPlayState();
}
function syncVideoPopup() {
	vid.currentTime=vid_popup.currentTime;
	vid.play();
	vid_popup.pause();
	playing_status=true;
	setPlayState();
}


