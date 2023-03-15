var el = document.getElementsByClassName("stats_shares_btn");

function sharedBTN( x ) {
	el[x-1].className = "stats_shares_btn on";
}