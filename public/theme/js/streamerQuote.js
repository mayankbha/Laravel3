var streamerContext = document.getElementsByClassName('opinions_context');
var streamer = document.getElementsByClassName('streamer');

function changeQuote(y) {
	for (var i = 0; i < streamer.length; i++) {
		if (i == (y-1)) {
			streamer[i].className = 'streamer selected';
			streamerContext[i].style.cssText = "opacity: 1;";
		}
		else {
			streamer[i].className = 'streamer';
			streamerContext[i].style.cssText = "opacity: 0;";
		}
		
	}
}