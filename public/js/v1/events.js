var main_stream_player_view_status = 1;
var ajax_view_status = 1;
var time_refresh = 10000;
var main_stream_video_config = {
    desktop: {
        width: 740,
        height: 430,
    },
    mobile: {}
};
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
var divplay = "card_image";
var link = caster_mode_url;
var poster = $("#poster").val();
var main_stream_player;
var sub_stream_player;
var conf;
var reload_main_timeout = [];
var last_time_tracking;
jwplayer.key = "2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
$(document).ready(function () {

    if (default_player == "bit") {
        main_stream_player = typeof bitmovin !== "undefined" ? bitmovin.player(divplay) : bitdash(divplay);
        conf = {
            key: '0595f8f6-4d6d-4546-b49a-378985bfca48',
            source: {
                hls: link,
                // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
                poster: poster,
                vr: {startupMode: '2d', startPosition: 180}
            },
            style: {aspectratio: '2:1'},
            playback: {autoplay: true}
        };
        bit_setup_func = function () {
            main_stream_player.setup(conf).then(function (value) {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    if (window.innerHeight < window.innerWidth) {
                        main_stream_player.enterFullscreen();
                    }
                }
                var duration = main_stream_player.getDuration();
                $(".bmpui-ui-watermark").hide();
            }, function (reason) {
                setTimeout(function () {
                    bit_setup_func();
                }, 5000);
                console.log('Error while creating bitdash player instance');
            });
        };
        bit_setup_func();
        main_stream_player.addEventHandler(bitmovin.player.EVENT.ON_TIME_CHANGED, function (obj) {
            time = parseInt(obj.time / 20) * 20;
            if (obj.time >= time && time + 1 > obj.time && time > 0) {
                if (ajax_view_status == 1) {
                    ajax_view_status = 0;
                    var request = $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
                        },
                        url: "/event/view",
                        method: "POST",
                        data: {
                            id: event_stream_id,
                            update: main_stream_player_view_status,
                        }
                    });
                    request.done(function (data) {
                        $('#view_numb').html(data.view_numb);

                    });

                    request.fail(function (jqXHR, textStatus) {
                        show_msg(textStatus, snackbar);
                    });
                    if (main_stream_player_view_status == 1) {
                        main_stream_player_view_status = 0;
                    }
                }
            }
            if (time + 1 < obj.time && time > 0) {
                ajax_view_status = 1;
            }
        });
        main_stream_player.load_new_source = function (stream_obj) {
            conf.source.hls = stream_obj.url;
            this.load({
                hls: stream_obj.url,
                // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
                vr: {startupMode: '2d', startPosition: 180}
            });
        }
    }
    else {
        main_stream_width = $('#card_image').parent().width();
        main_stream_height = main_stream_width * (9 / 18);
        conf = {
            hlshtml: true,
            image: poster,
            autostart: true,
            width: main_stream_width,
            height: main_stream_height,
            playlist: [{
                stereomode: 'monoscopic',
                file: link,
            }]
        };
        jw_setup_func = function () {
            last_time_tracking = 0;
            main_stream_player = jwplayer('card_image').setup(conf);
            main_stream_player.on('error', function (event) {
                console.log('error player');
                reload_main_timeout.push(setTimeout(function () {
                    main_stream_player.load_new_source({url:link});
                }, 5000));
            });
            main_stream_player.on('buffer', function (event) {
                console.log('3-----');
                if (event.reason == 'stalled') {
                    reload_main_timeout.push(setTimeout(function () {
                        main_stream_player.load_new_source({url:link});
                    }, 5000));
                }
            });
            main_stream_player.on('time', function (event) {
                if (last_time_tracking == event.position && last_time_tracking != 0) {
                    //console.log('1-----');
                    reload_main_timeout.push(setTimeout(function () {
                        main_stream_player.load_new_source({url:link});
                    }, 5000));
                    last_time_tracking = -1;
                }
                else if (last_time_tracking !=  -1 ){
                    //console.log('2---------');
                    last_time_tracking = event.position;
                }
            });
            main_stream_player.on('bufferChange',function (event) {
                console.log(event);
            });


            main_stream_player.on('play', function (event) {
                for (i=0;i < reload_main_timeout.length; i++){
                    clearTimeout(reload_main_timeout[i]);
                }
                reload_main_timeout = [];
            });
            setInterval(function () {
                if (last_time_tracking == -1){
                    reload_main_timeout.push(setTimeout(function () {
                        main_stream_player.load_new_source({url:link});
                    }, 5000));
                    last_time_tracking = 0;
                }
            },5000);
        };
        jw_setup_func();

        $(window).resize(function () {
            main_stream_width = $('#card_image').parent().width();
            main_stream_height = main_stream_width * (9 / 16);
            main_stream_player.resize(main_stream_width, main_stream_height);

        });

        main_stream_player.on('time', function (event) {
            time = parseInt(event.position / 20) * 20;
            if (event.position >= time && time + 1 > event.position && time > 0) {
                if (ajax_view_status == 1) {
                    ajax_view_status = 0;
                    var request = $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
                        },
                        url: "/event/view",
                        method: "POST",
                        data: {
                            id: event_stream_id,
                            update: main_stream_player_view_status,
                        }
                    });
                    request.done(function (data) {
                        $('#view_numb').html(data.view_numb);

                    });

                    request.fail(function (jqXHR, textStatus) {
                        show_msg(textStatus, snackbar);
                    });
                    if (main_stream_player_view_status == 1) {
                        main_stream_player_view_status = 0;
                    }
                }
            }
            if (time + 1 < event.position && time > 0) {
                ajax_view_status = 1;
            }
        });
        main_stream_player.load_new_source = function (stream_obj) {
            link = stream_obj.url;
            conf.playlist[0].file = stream_obj.url
            this.load({
                file: link,
                stereomode: 'monoscopic',
            });
        }
        jwplayer('card_image').onDisplayClick(function(){
            jwplayer('card_image').play();
        });
        main_stream_player.on('ready',function () {
            var playButton = $('#card_image').find('div[class="jw-group jw-controlbar-left-group jw-reset"]');
            playButton.remove();
            $('#card_image').find('div[class="jw-icon jw-icon-tooltip jw-icon-volume jw-button-color jw-reset"]').remove();
        })
    }

});
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
        jwplayer('video-jumb').onDisplayClick(function(){
            jwplayer('video-jumb').play();
        });
        sub_stream_player.on('ready',function () {
            var playButton = $('#video-jumb').find('div[class="jw-group jw-controlbar-left-group jw-reset"]');
            playButton.remove();
        });

    };
    jumbo_setup_func();


}


var camera_position = 0;
function mapSpots(div) {
    var allSpots = document.getElementsByClassName('map_spot');
    for (var i = allSpots.length - 1; i >= 0; i--) {
        allSpots[i].className = "map_spot";
    }
    div.className = "map_spot selected"
    stream_obj = $(div).data("stream");
    link = stream_obj.url;
    main_stream_player.load_new_source(stream_obj);
    $(".map_name").html(stream_obj.name);
    camera_position = stream_obj.key;
}


var shareBtn = document.getElementsByClassName('livestream_share')[0];
var shareOptions = document.getElementsByClassName('livestream_share_options')[0];

window.addEventListener('click', function (e) {
    if (typeof shareBtn != "undefined") {
        if (shareBtn.contains(e.target)) {
            shareOptions.className = "livestream_share_options show";
        } else {
            shareOptions.className = "livestream_share_options";
        }
    }
});

var overlay360 = document.getElementsByClassName('overlay_360_container')[0];
window.onload = function () {
    if (typeof overlay360 != "undefined") {
        overlay360.style.cssText = "opacity : 1";
        overlay360.onclick = function () {
            hideOverlay();
        };
        overlay360.onmouseover = function () {
            hideOverlay();
        };
    }

};
function showOverlay() {
    overlay360.style.cssText = "opacity : 1";
    overlay360.className = "overlay_360_container x_show"
}
function hideOverlay() {
    overlay360.style.cssText = "";
    overlay360.className = "overlay_360_container x_hide"
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
var caster_mode = 1;
function changeCaster(div){

    if (caster_mode == 1) {
        $(".videos_switch_right").removeClass("normal").addClass("compact");
        caster_mode = 0;
        $(".minimap-overlay").css('display','none');
        $(".map").show();
        main_stream_player.load_new_source($('.map_spot.selected').data("stream"));
        clearCasterModeInterval();
    }
    else {
        $(".videos_switch_right").removeClass("compact").addClass("normal");
        caster_mode = 1;
        $(".minimap-overlay").css('display','block');
        $(".map").hide();
        main_stream_player.load_new_source({url:caster_mode_url});
        setCasterModeInterVal();

    }
}

var caster_mode_interval;
function setCasterModeInterVal(){
    caster_mode_interval = setInterval(function () {
        console.log('Interval reload caster stream');
        main_stream_player.load_new_source({url:caster_mode_url});
    },caster_mode_interval_time);
}
function clearCasterModeInterval() {
    clearInterval(caster_mode_interval);
}

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
        if (data.status == 100){
            location.reload();
        }
        if (data.status == 1){
            $('.map').html(data.content);
            current_map = data.current_map;
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
    setCasterModeInterVal();
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