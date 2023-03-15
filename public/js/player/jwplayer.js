var player;
var player_false_count = 0;
var player_interval;
var duration;
var first_error_setup = 1;
var referer = getParameterByName("ref");
var first_share_setup = 1;

$(document).ready(function () {
    var autoload = 1;

    var link = $("#link").val();
    var poster = $("#poster").val();
    jwplayer.key = "2WXItcuns5JmZUeFSbIWZxLpXxvI2YRYMDrdnRSBnSI=";
    player = jwplayer("card_image").setup({
        "file": link,
        "image": poster,
        "autostart": true,
        "aspectratio": "16:9",
        "width": "100%",
    });
    jwplayer("card_image").onPlay(function () {
        if(player_type != 'bitmovin') {
            autoload = 0;
            $('.overlay').hide();
            player.setControls(true);
        }

        duration = jwplayer("card_image").getDuration();
        countView(duration, vtime);
    });
    jwplayer("card_image").on('error', function (event) {
        if (first_error_setup){
            ga('send', 'event', 'JW_PLAYER_SETUP_ERROR', 'setup error', 'jw player setup error');
            first_error_setup = 0;
        }
    });

    jwplayer("card_image").on('complete', function (event) {
        autoload = 1;

        if (first_share_setup){
            $('.overlay').show();
            //show_modal_content();
            //first_share_setup = 0;

            if(player_type != 'bitmovin') {
                $('#play_icon-'+next_streams_video).show();
                player.setControls(false);

                var options = {
                                height: "100px",
                                width: "100px",
                                line_width: 6,
                                color: "#fff",
                                starting_position: 5,
                                percent: 5,
                                text: ""
                            }

                var progress_circle = $("#progress-circle-"+next_streams_video).gmpc(options);
                progress_circle.gmpc('animate', 100, 10000);

                setTimeout(function() {
                    if(autoload == 1)
                        window.location.href = url+'/w?v='+next_streams_video;
                }, 10000);
            }
        }
    });

    //set speed
    var trident = !!navigator.userAgent.match(/Trident\/7.0/);
    var net = !!navigator.userAgent.match(/.NET4.0E/);
    var IE11 = trident && net
    var IEold = ( navigator.userAgent.match(/MSIE/i) ? true : false );
    var theVideo;

    // show speed
    jwplayer("card_image").onReady(function () {
        var myLogo = document.createElement("div");
        myLogo.setAttribute('class', 'jw-icon jw-icon-tooltip jw-icon-speed jw-button-color jw-reset jw-off');
        myLogo.id = "speed-video";
        myLogo.setAttribute('style', "color: #ffffff; margin-top: -5px;");
        myLogo.setAttribute('tabindex', '0');
        myLogo.setAttribute('aria-label', 'Speed');
        myLogo.setAttribute('aria-hidden', 'true');
        myLogo.innerHTML = "&#x25F7;";
        var html = '<div class="jw-overlay jw-reset"><ul class="jw-menu jw-background-color jw-reset"> \
     <li onclick="setSpeed(this,1)" class="jw-text jw-option jw-item-0 jw-reset jw-active-option">Auto</li> \
     <li onclick="setSpeed(this,0.25)" class="jw-text jw-option jw-item-1 jw-reset">0.25x</li> \
     <li onclick="setSpeed(this,0.5)" class="jw-text jw-option jw-item-2 jw-reset">0.5x</li> \
     <li onclick="setSpeed(this,1)" class="jw-text jw-option jw-item-2 jw-reset">1x</li> \
     <li onclick="setSpeed(this,1.5)" class="jw-text jw-option jw-item-3 jw-reset">1.5x</li> \
     <li onclick="setSpeed(this,2)" class="jw-text jw-option jw-item-4 jw-reset">2x</li> \
     </ul></div>';
        document.getElementsByClassName('jw-controlbar-right-group')[0].appendChild(myLogo);
        $("#speed-video").append(html);
        $("#speed-video").insertAfter($(".jw-icon-hd"));
        $("#speed-video").hover(
            function () {
                $(this).removeClass("jw-off");
                $(this).addClass("jw-open");
            }, function () {
                $(this).removeClass("jw-open");
                $(this).addClass("jw-off");
            }
        );
        set_next_button_position();
    });

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

show_modal_content = function (msg,title) {
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

