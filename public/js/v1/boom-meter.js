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