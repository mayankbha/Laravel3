$(document).ready(function () {
    var divplay = "card_image";
    var link = $("#link").val();
    var poster = $("#poster").val();
    var player = typeof bitmovin !== "undefined" ? bitmovin.player(divplay) : bitdash(divplay);
    ;
    conf = {
        key: '0595f8f6-4d6d-4546-b49a-378985bfca48',
        source: {
            hls: link,
            // progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
            poster: poster,
            vr: {startupMode: '2d', startPosition: 180}
        },
        style: {aspectratio: '16:9'},
        playback: {autoplay: true}
    };
    player.setup(conf).then(function (value) {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            player.setVideoQuality('480_1786000');
            if (window.innerHeight < window.innerWidth) {
                player.enterFullscreen();
            }
        }
        var duration = player.getDuration();
        console.log(duration);
        countView(duration, vtime);
        console.log('Successfully created bitdash player instance');
        $(".bmpui-ui-watermark").hide();
    }, function (reason) {
        console.log('Error while creating bitdash player instance');
    });

    $(window).on('orientationchange', function (event) {
        if (orientation == 90) {
            player.enterFullscreen();
        }
    });
    set_next_button_position();


});