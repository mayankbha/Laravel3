var nav = document.getElementsByTagName('nav')[0];
var navDropdown = document.getElementsByClassName('nav_dropdown')[0];
var user = document.getElementsByClassName('nav_link user')[0];
var signIn = document.getElementsByClassName('nav_link sign_in')[0];
var windowWidth = window.innerWidth;
var previousTop = 0;
var profileOn = 0;

function profileSettings(div) {
  var className = div.getAttribute("class");
  if (className=="nav_link sign_in show") {
    user.className = "nav_link user show";
    signIn.className = "nav_link sign_in";
    navDropdown.className = "nav_dropdown";
    profileOn = 0;
  }
  else if(className=="nav_link user show") {
    navDropdown.className = "nav_dropdown show";
    profileOn = 1;
  }
  else if (className=="dropdown_link sign_out") {
    user.className = "nav_link user";
    signIn.className = "nav_link sign_in show";
    navDropdown.className = "nav_dropdown";
    profileOn = 0;
  }
  else{
    navDropdown.className = "nav_dropdown";
    profileOn = 0;
  }
}

function onScroll(event){
  var offset = window.pageYOffset || document.documentElement.scrollTop;
  
  /* Mobile Menu */
  if (windowWidth <= 770) {
    if (offset < 50) {
      nav.style.cssText = "";
    }
    else if(profileOn == 1){
      nav.style.cssText = "background-color: rgba(31,33,46,0.9);";
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