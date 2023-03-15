/*function showFlag(div) {
    var className = div.getAttribute("class");
    if (className == "stats_options_btn") {
        div.parentNode.className = "stats_options show";
    }
    else {
        div.parentNode.className = "stats_options";
    }
}*/

function showFlag(div) {
  var className = div.getAttribute("class");
  if(className=="stats_options_btn" && div.parentNode.className == "stats_options" ) {
    div.parentNode.className = "stats_options show";
  }
  else{
    div.parentNode.className = "stats_options";
  }
}