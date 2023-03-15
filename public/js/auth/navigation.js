function profileSettingsCustom(div) {
  /*var className = div.getAttribute("class");
  if (className=="nav_link sign_in show")  {
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
  }*/
  var className = div.getAttribute("class");
  console.log(navDropdown.className);
  if (navDropdown.className == "nav_dropdown show")
  {
    navDropdown.className = "nav_dropdown hide";
  }
  else
  {
    navDropdown.className = "nav_dropdown show";
  }
  console.log(navDropdown.className);
}