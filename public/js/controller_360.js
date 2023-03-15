function keyUp(){
	
	var e = new Event("keydown");
  e.key="w";    // just enter the char you want to send 
  e.keyCode=e.key.charCodeAt(0);
  e.which=e.keyCode;
  e.altKey=false;
  e.ctrlKey=false;
  e.shiftKey=false;
  e.metaKey=false;
  e.bubbles=false;
  document.dispatchEvent(e);
}
function keyDown(){
	
	jwplayer("video-360").addEventListener("mousedown", this.hideMenuHandler);
}
function keyLeft(){
	
	$(this).trigger(
        jQuery.Event( 'keydown', { keyCode: 65, which: 65 } )
    );
}
function keyRight(){

	
	$(this).trigger(
        jQuery.Event( 'keydown', { keyCode: 68, which: 68 } )
    );
}
