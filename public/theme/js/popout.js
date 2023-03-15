var popoutBox = document.getElementsByClassName('header_popout_holder')[0];

function popoutShow() {
	popoutBox.style.cssText = "opacity: 1; z-index: 1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s 0s;";
}
function popoutHide() {
	popoutBox.style.cssText = "opacity: 0; z-index: -1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s .5s;";
}