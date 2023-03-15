var nav = document.getElementsByTagName('nav')[0];
var windowWidth = window.innerWidth;
var previousTop = 0;


function onScroll(event){
  var offset = window.pageYOffset || document.documentElement.scrollTop;
  
  /* Mobile Menu */
  if (windowWidth <= 770) {
    if (offset < 50) {
      nav.style.cssText = "";
    }
	  else if(offset > previousTop){
      nav.style.cssText = "transform: translate(0,-100%);background-color: rgba(31,33,46,0.9);";
    }
    else {
      nav.style.cssText = "background-color: rgba(31,33,46,0.9);";
    }
  }
  /* Desktop Nav */
  else {
	if (offset > 50) {
	  nav.style.cssText = "background-color: rgba(31,33,46,0.9);";
	}
	else {
	  nav.style.cssText = "";
	}
  }
  
  previousTop = offset;
}

window.addEventListener('scroll', onScroll);