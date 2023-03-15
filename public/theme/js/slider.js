var sliderCard = document.getElementsByClassName('video');
var currentCard = 0;


function sliderOnLoad() {
	for (var i = 0; i < sliderCard.length; i++) {
		sliderCard[i].style.cssText = "transform: translate("+i+"00%, 0);";
	}
}

function sliderLeft() {
	currentCard--;
	if (currentCard >= 0) {
		for (var i = 0; i < sliderCard.length; i++) {
			if (i < currentCard) {
				sliderCard[i].style.cssText ="transform: translate(-100%, 0);";
			}
			else {
				sliderCard[i].style.cssText = "transform: translate("+(i-currentCard)+"00%, 0);";
			}
			
		}
	}
	else {
		currentCard= 0;
	}
}

function sliderRight() {
	currentCard++;
	if (currentCard <= sliderCard.length -3) {
		for (var i = 0; i < sliderCard.length; i++) {
			if (i < currentCard) {
				sliderCard[i].style.cssText ="transform: translate(-100%, 0);";
			}
			else {
				sliderCard[i].style.cssText = "transform: translate("+(i-currentCard)+"00%, 0);";
			}
			
		}
	}
	else {
		currentCard= (sliderCard.length-3);
	}
}

window.addEventListener('load', sliderOnLoad);