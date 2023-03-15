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
var popoutBox = document.getElementsByClassName('header_popout_holder')[0];

function popoutShow() {
	popoutBox.style.cssText = "opacity: 1; z-index: 1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s 0s;";
}
function popoutHide() {
	popoutBox.style.cssText = "opacity: 0; z-index: -1; transition: opacity .5s cubic-bezier(0.46, 0.03, 0.52, 0.96), z-index 0s .5s;";
}
function gettimezone()
{
	var offset = new Date().getTimezoneOffset();
	var minutes = Math.abs(offset);
	var hours = Math.floor(minutes / 60);
	var prefix = offset <= 0 ? "+" : "-";
	var timezone = prefix+hours;
	$.post(url+"/set_userzone",
            {
                user_zone:timezone
            },
         function(data){
         	 
           if(data==false)
            location.reload();
           else
            console.log("Timezone detected");
           

         }
    );
}

$(document).ready(function(){
    gettimezone();
});
function share(item) {
    //ga('send', 'event', 'Buttons', 'click', 'Share video');
    var link = $(item).attr('href');
    var popup = window.open(link, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    var pollTimer = window.setInterval(function () {
        if (popup.closed !== false) { // !== is required for compatibility with Opera
            window.clearInterval(pollTimer);
            getShare();
        }
    }, 200);
}

function likeVideo() {
    $.post(url + "/likevideo",
        {
            vcode: vcode
        },
        function (data) {
            $('#like_numb').html(data.like);
            like_state = data.link_state;
            like_state = !like_state;
            setLikeState(like_state);
        }
    );
}

function getShare() {
    $.post(url + "/getshare",
        {
            vcode: vcode
        },
        function (data) {
            $('#share_numb').html(data);

        }
    );
}
function setLikeState(like_state) {
    if (like_state == 1) {

        $("#liked").addClass("show");
        $("#not_like").addClass("hide");

        $("#liked").removeClass("hide");
        $("#not_like").removeClass("show");
        $("#btn_like").attr("title", "Unlike this video");
        //ga('send', 'event', 'Buttons', 'click', 'Like video');
    }
    else {
        $("#liked").addClass("hide");
        $("#not_like").addClass("show");

        $("#liked").removeClass("show");
        $("#not_like").removeClass("hide");
        $("#btn_like").attr("title", "Like this video");
        //ga('send', 'event', 'Buttons', 'click', 'Unlike video');
    }
}
function setPlayState() {
    if (playing_status == true) {
        $("#pause_icon").addClass("pause");
        $("#pause_icon").removeClass("play_triangle");
    }
    else {
        $("#pause_icon").addClass("play_triangle");
        $("#pause_icon").removeClass("pause");
    }

}
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
show_modal_content = function (msg,title) {
    $(".modal_two_main").html(msg);
    $(".modal_two").fadeIn().addClass('modal_two_zindex');
    $(".modal_bg").addClass('modal_bg_show');
    $(".modal_bg").click(function () {
        hide_modal();
    });
    $(document).keyup(function(e) {
        if (e.keyCode == 27) { // escape key maps to keycode `27`
            hide_modal();
        }
    });

}
hide_modal = function () {
    $(".modal_bg").removeClass('modal_bg_show');
    $(".modal_two").fadeOut().removeClass('modal_two_zindex');
}

set_selected_skin = function (obj) {
    $(obj).addClass("skin_button_select");
    $(obj).addClass("boom_meter_get_button_x");
    $(obj).removeClass("boom_meter_get_button");
    $(obj).html('<div>' +
        '<img src="'+boom_mete_check_mark_img+'">' +
    '</div>SELECTED');
};
remove_selected_skin = function () {
    $(".boom_meter_get_button_x").each(function (i, obj) {
        if ($(obj).hasClass("skin_button_select")) {
            $(obj).removeClass("skin_button_select");
            $(obj).removeClass("boom_meter_get_button_x");
            $(obj).addClass("boom_meter_get_button");
            $(obj).html("GET");
        }
    });
};
trigger_selected_event = function(obj){
    $(".boom_meter_get_button").each(function (i, obj) {
        $(obj).click(function (e) {
            e.preventDefault();
            if (!$(obj).hasClass("skin_button_select")) {
                remove_selected_skin();
                set_selected_skin(obj);
                $.get($(obj).attr('data-href'), function (data) {
                    if (data.status == 0) {
                        show_modal_content(data.msg);
                    }
                    else if (data.status == 1) {
                    }
                    //trigger_selected_event();
                });
                trigger_selected_event();
            }
        });
    });
};
function popupState(x) {
    if (x.className == "boom_meter_fun unlock") {
        x.parentNode.parentNode.className = "boom_meter_item locked details";
    }
    else if (x.className == "boom_meter_modal_close") {
        x.parentNode.parentNode.parentNode.parentNode.className = "boom_meter_item locked";
    }
}
//# sourceMappingURL=main-footer.js.map
