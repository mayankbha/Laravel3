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