/**
 * Created by tannn on 5/12/17.
 */
var next_button = $(".next_video");
next_button.click(function (e) {
    window.location = next_video_link;
});
$(document).ready(function(){
    set_next_button_position();
    $(window).resize(function () {
        set_next_button_position();
        set_next_button_dimension();
    });
    set_next_button_dimension();
});
set_next_button_position = function(){
    video_with = $(".video-detail").width();
    window_with = $(window).width();
    if ((window_with-video_with)/2  > 100){
        next_button_right = (window_with-video_with)/2 - 50;
        next_button.css({right:next_button_right});
    }
    else{
        next_button.css({right:0});
    }
    next_button_top = $('#card_image').height() / 2 + $('#card_image').offset().top ;
    next_button.css({top:next_button_top});
    if (next_button.css('display') == 'none'){
        next_button.fadeIn();
    }

};

set_next_button_dimension = function () {
    next_button.css({height:$('#card_image').height()/2-30,width:32})
    if (next_button.height() >= 150){
        next_button.css({height:150,width:32})
    }
}