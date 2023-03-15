var time_refresh = 10000;
var sub_stream_video_config = {
    desktop: {
        normal_small: {
            width: 515,
            height: 312,
        },
        normal_mid: {
            width: 665,
            height: 312,
        },
        normal_big: {
            width: 800,
            height: 312,
        },
        jumbotron: {
            width: 293,
            height: 160,
        }

    },
    mobile: {}
};

function get_jumbo_size(){
    var jumbo_size = getParameterByName('jumbo_size');
    if (jumbo_size == null || jumbo_size == ''){
        jumbo_size = 'mid'
    }
    if (jumbo_size == 'small'){
        var _sub_stream_width = sub_stream_video_config.desktop.normal_small.width;
    }
    else if (jumbo_size == 'big'){
        var _sub_stream_width = sub_stream_video_config.desktop.normal_big.width;
    }
    else {
        var _sub_stream_width = sub_stream_video_config.desktop.normal_mid.width;
    }
    return _sub_stream_width;
}

var sub_stream_width = get_jumbo_size();
var caster_mode_url = $("#link").val();
var link = caster_mode_url;
var poster = $("#poster").val();
var main_stream_player;
var sub_stream_player;
var conf;
jwplayer.key = "2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
if (show_jumbo) {
    var reload_jumbo_timeout;
    var link_jumb = $("#link-jumb").val();
    var poster_jumb = $("#poster-jumb").val();
    jumbo_setup_func = function () {
        sub_stream_player = jwplayer("video-jumb").setup({
            "file": link_jumb,
            "image": poster_jumb,
            "autostart": true,
            "width": sub_stream_width,
            "height": sub_stream_width *(9 / 16 ),
        });
        sub_stream_player.on('error', function () {
            console.log('error jumbotron');
            reload_jumbo_timeout = setTimeout(function () {
                jumbo_setup_func();
            }, 5000);
        });
        sub_stream_player.on('buffer', function (event) {
            if (event.reason == 'stalled') {
                reload_jumbo_timeout = setTimeout(function () {
                    jumbo_setup_func();
                }, 5000);
            }
            else {
                clearTimeout(reload_jumbo_timeout);
            }
        });
        sub_stream_player.on('play', function (event) {
            clearTimeout(reload_jumbo_timeout);
        });

    };
    jumbo_setup_func();


}

var jumbo_mode = 0;
function changeMode(div) {
    if (show_jumbo) {
        if (jumbo_mode == 0) {
            $(".videos_switch").removeClass("normal").addClass("compact");
            jumbo_mode = 1;
            sub_stream_width = sub_stream_video_config.desktop.jumbotron.width;
            sub_stream_player.resize(sub_stream_video_config.desktop.jumbotron.width, sub_stream_video_config.desktop.jumbotron.height);
        }
        else {
            $(".videos_switch").removeClass("compact").addClass("normal");
            jumbo_mode = 0;
            sub_stream_width = get_jumbo_size();
            sub_stream_player.resize(sub_stream_width, sub_stream_width*(9/16));
        }
    }
}

var camera_position = 0;
request_check_map_change = function(){
    var request = $.ajax({
        headers: {
            'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
        },
        url: "/event/map",
        method: "POST",
        data: {
            input: stream_input,
            quality : stream_quality,
            current_map : current_map,
            camera_position : camera_position
        }
    });
    request.done(function (data) {
        if (data.status == 1000){
            location.reload();
        }
        if (data.status == 1){
            location.reload();
        }
    });

    request.fail(function (jqXHR, textStatus) {
        show_msg(textStatus, snackbar);
    });
};

$(document).ready(function () {
    $(".card_likes img").css('cursor', 'pointer');
    $(".card_likes img").click(function (e) {
        var request = $.ajax({
            url: "/event/like",
            method: "POST",
            data: {
                id: $(this).data('id'),
            }
        });
        request.done(function (data) {
            $('#like_numb').html(data.like_numb);

        });

        request.fail(function (jqXHR, textStatus) {
            show_msg(textStatus, snackbar);
        });
    });

    setInterval(function(){
        request_check_map_change();
    },time_refresh)

    $(window).scrollTop(100);
});

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}