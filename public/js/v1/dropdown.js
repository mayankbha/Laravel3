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

// TEAM
/*var dropdownTeam = document.getElementsByClassName('team_sort_dropdown')[0],
	btnTeam = document.getElementsByClassName('team_sort_button'),
	teamValue;

window.addEventListener('click', function(e){
	
	if (btnTeam[0].contains(e.target) && dropdownTeam.className == "team_sort_dropdown"){
  		dropdownTeam.className = "team_sort_dropdown show";
  	}
  	else if (btnTeam[1].contains(e.target)){
  		teamValue = btnTeam[1].innerText;
  		dropdownTeam.className = "team_sort_dropdown";
  		btnTeam[1].innerText = btnTeam[0].innerText;
  		btnTeam[0].innerText = teamValue;
 	}
 	else if (btnTeam[2].contains(e.target)){
  		teamValue = btnTeam[2].innerText;
  		dropdownTeam.className = "team_sort_dropdown";
  		btnTeam[2].innerText = btnTeam[0].innerText;
  		btnTeam[0].innerText = teamValue;
 	}
 	else {
 		dropdownTeam.className = "team_sort_dropdown";
 	}
});*/