var main_slick = {limit: 10, page: 1, running: 0};
var sub_slick = [];
var like_stack = [];

init_like_main_video_event = function () {
    $('.card_likes img').off('click').on('click',function(e){
        __vcode = $(this).parent().attr('data-vcode');
        like_image_and_set_state(__vcode,$(this).parent());
    });
};
init_like_image_event = function () {
    $('.card_likes_new img').off('click').on('click',function(e){
        __vcode = $(this).parent().attr('data-vcode');
        like_image_and_set_state(__vcode,$(this).parent());
    });
};

like_image_and_set_state = function (__vcode, el) {
    if (like_stack[__vcode] == null){
        like_stack[__vcode] = {running:0};
    }
    if (like_stack[__vcode].running == 0){
        like_stack[__vcode].running = 1;
        $.post(url + "/likeimage",
            {
                icode: __vcode
            },
            function (data) {
                el.find('span:first').html(data.like);
                like_stack[__vcode].running = 0;
            }
        );
    }

};

init_el_carousel = function (el, x_url) {
    el.slick({
        infinite: false,
        responsive: [
            {
                breakpoint: 1025,
                settings: {
                    initialSlide: 0
                }
            },
            {
                breakpoint: 601,
                settings: {
                    initialSlide: 0
                }
            }
        ]
    });
    el.on("afterChange", function (event, slick, current_slide) {
        elm = $(this);
        if (current_slide == slick.slideCount - 6) {
            if (sub_slick[elm.selector] == null) {
                sub_slick[elm.selector] = {limit: 10, page: 1, running: 0};
            }
            if (sub_slick[elm.selector].running == 0) {
                sub_slick[elm.selector].running = 1;
                ajax_url = elm.data('url') + "&page=" + (sub_slick[el.selector].page + 1) + "&limit=" + sub_slick[el.selector].limit;
                var request = $.ajax({
                    url: ajax_url,
                    method: "GET",
                });
                request.done(function (msg) {
                    slick.slickAdd(msg.content);
                    sub_slick[el.selector].page = sub_slick[el.selector].page + 1;
                    if (msg.count == 0) {
                        elm.off('afterChange');
                    }
                    sub_slick[elm.selector].running = 0;
                    init_like_video_event();
                });

                request.fail(function (jqXHR, textStatus) {
                    console.log(textStatus);
                });
            }


        }
    });
};