var inputBox = document.getElementsByTagName('input')[0];
var subscribeBox = document.getElementsByClassName('subscribe_input')[0];
var subscribeBoxTitle = document.getElementsByClassName('subscribe_box_about')[0];
var thanksMessagee = document.getElementsByClassName('subscribe_thanks')[0];
var errorMessagee = document.getElementsByClassName('subscribe_error')[0];



function validateEmail() 
{
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (re.test(inputBox.value)) {
    	subscribeBox.style.cssText = "opacity: 0; z-index: 0;";
    	thanksMessagee.style.cssText = "color: rgba(134,136,152,1); z-index: 0;";
    	errorMessagee.style.cssText = "color: rgba(200,50,50,0";
    	if (subscribeBoxTitle != undefined) {
    		subscribeBoxTitle.style.cssText = "opacity: 0;";
    	}
    }
    else {
    	errorMessagee.style.cssText = "color: rgba(200,50,50,.8)";
    }
}
