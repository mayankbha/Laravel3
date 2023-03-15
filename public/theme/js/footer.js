var footerElement = document.getElementsByTagName('footer')[0];

var screenHeight2 = document.documentElement.clientHeight;
var bodyHeight = document.getElementsByTagName('body')[0].offsetHeight;
var isBottom = 0;

function footerReposition() {
	screenHeight2 = document.documentElement.clientHeight;
	bodyHeight = document.getElementsByTagName('body')[0].offsetHeight + (footerElement.offsetHeight * isBottom);
	if ( bodyHeight < screenHeight2) {
		footerElement.style.cssText = 'position: absolute; width: 100%; bottom: 0; left: 0';
		isBottom = 1;
	}
	else {
		footerElement.style.cssText = '';
		isBottom = 0;
	}
}

window.addEventListener('load', footerReposition);
window.addEventListener('resize', footerReposition);