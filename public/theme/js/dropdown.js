var dropdownGame = document.getElementsByClassName('game_dropdown')[0];
var btnGame = document.getElementsByClassName('game_button');
var dropdownSort = document.getElementsByClassName('sort_dropdown')[0];
var btnSort = document.getElementsByClassName('sort_button');

var gameClick =0 ,
	sortClick = 0;


function gameDropdown(y) {
	if (gameClick%2 == 0) {
		dropdownGame.className = 'game_dropdown show';
		btnGame[0].style.cssText = 'color: white;background-color: rgba(20,21,31,.5);border-color: rgba(73,75,87,1);';
	}
	else {
		dropdownGame.className = 'game_dropdown';
		btnGame[0].style.cssText = '';
	}
	gameClick++;
	sortClick=0;
	dropdownSort.className = 'game_dropdown';
	btnSort[0].style.cssText = '';
}
function sortDropdown(y) {
	if (sortClick%2 == 0) {
		dropdownSort.className = 'game_dropdown show';
		btnSort[0].style.cssText = 'color: white;background-color: rgba(20,21,31,.5);border-color: rgba(73,75,87,1);';
	}
	else {
		dropdownSort.className = 'game_dropdown';
		btnSort[0].style.cssText = '';
	}
	sortClick++;
	gameClick=0;
	dropdownGame.className = 'game_dropdown';
	btnGame[0].style.cssText = '';
}