var main_slick = {limit: 10, page: 1, running: 0};
var sub_slick = [];
var like_stack = [];
var SLICK_RESPONSIVE_CONFIG = [
    {
        breakpoint: 2500,
        settings: {
            initialSlide: 0,
            slidesToShow: 4,
            slidesToScroll: 4,
            variableWidth: true,
            centerMode: false,
        }
    },
    {
        breakpoint: 1920,
        settings: {
            initialSlide: 0,
            slidesToShow: 4,
            slidesToScroll: 4,
            variableWidth: true,
            centerMode: false,
        }
    },
    {
        breakpoint: 1025,
        settings: {
            initialSlide: 0,
            slidesToShow: 4,
            slidesToScroll: 4,
            variableWidth: true,
            centerMode: false,
        }
    },
    {
        breakpoint: 769,
        settings: {
            initialSlide: 0,
            slidesToShow: 3,
            slidesToScroll: 3,
            variableWidth: false,
            centerMode: false,
            infinite: false,
        }
    },
    {
        breakpoint: 425,
        settings: {
            initialSlide: 0,
            slidesToShow: 1,
            variableWidth: true,
            centerMode: false,
            infinite: false,
            /*slidesToShow: 2,
            slidesToScroll: 2,
            variableWidth: false,
            centerMode: false,*/
        }
    },
    {
        breakpoint: 321,
        settings: {
            initialSlide: 0,
            slidesToShow: 1,
            variableWidth: true,
            centerMode: false,
            infinite: false,
        }
    }
];
ajax_render_video_content = function (msg){
    msg.content =  $.map(msg.content, function(value, index) {
        return [value];
    });
    $.views.settings.delimiters("[%", "%]");
    var template = $.templates("#video_item_tpl");
    var htmlOutput = template.render(msg);
    return htmlOutput;
};
init_like_video_event = function () {
    $('.card_likes_new img').off('click').on('click', function (e) {
        __vcode = $(this).parent().attr('data-vcode');
        like_video_and_set_state(__vcode, $(this).parent());
    });
};
init_like_main_video_event = function () {
    $('.card_likes img').off('click').on('click', function (e) {
        __vcode = $(this).parent().attr('data-vcode');
        like_video_and_set_state(__vcode, $(this).parent());
    });
};

like_video_and_set_state = function (__vcode, el) {
    if (like_stack[__vcode] == null) {
        like_stack[__vcode] = {running: 0};
    }
    if (like_stack[__vcode].running == 0) {
        like_stack[__vcode].running = 1;
        $.post(url + "/likevideo",
            {
                vcode: __vcode
            },
            function (data) {
                el.find('span:first').html(data.like);
                like_stack[__vcode].running = 0;
            }
        );
    }

};

init_delete_video_event = function () {
    $(".stats_options_delete").off('click').on('click', function (e) {
        var snackbar = "snackbar";
        var r = confirm("Are you sure deleted this video ?");
        if (r == true) {
            container = $(this).attr('data-container');
            video_code = $(this).attr('data-vcode');
            video_id = $(this).attr('data-vid');

            x_url = "/video/remove/" + video_id;
            var request = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
                },
                url: x_url,
                method: "POST",
            });

            request.done(function (data) {

                if (data.state) {
                    index = $("#" + container).find("div[vcode=" + video_code + "]").first().attr("data-slick-index");
                    $("#" + container).slick('slickRemove', index);
                    show_msg(data.msg, snackbar);
                }
                else {
                    show_msg(data.msg, snackbar);
                }
            });

            request.fail(function (jqXHR, textStatus) {
                show_msg(textStatus, snackbar);
            });
        }
        else {
            show_delete($(this));
        }
    });
};

init_main_carousel = function () {
    $('.variable-width-big').on('init', function (slick) {
        if ($(window).width() > 1120) {
            $('.variable-width-big .slick-next').css({right: ($(window).width() - 1120) / 2})
            $('.variable-width-big .slick-prev').css({left: ($(window).width() - 1120) / 2})
        }
        else {

        }
        $(window).resize(function () {
            if ($(window).width() > 1120) {
                $('.variable-width-big .slick-next').css({right: ($(window).width() - 1120) / 2})
                $('.variable-width-big .slick-prev').css({left: ($(window).width() - 1120) / 2})
            }
            else {
                $('.variable-width-big .slick-next').css({right: 0})
                $('.variable-width-big .slick-prev').css({left: 0})
            }
        });

    });
    $('.variable-width-big').slick({
        infinite: true,
        speed: 300,
        slidesToShow: 2,
        centerMode: true,
        variableWidth: true,
        initialSlide: 0,
        autoplay: true,
        autoplaySpeed: 3000,
        responsive: [
            {
                breakpoint: 769,
                settings: {
                    infinite: true,
                    slidesToShow: 1,
                    centerMode: false,
                    variableWidth: false,
                    initialSlide: 0
                }
            }
        ]
    });
};

init_el_carousel = function (el, x_url) {
    if (el.find(".card").length == 5){
        el.append("<div class='card'></div>");
        el.data('off-after-change',1);
    }
    else{
        el.data('off-after-change',0);
    }
    el.slick({
        infinite: false,
        responsive: SLICK_RESPONSIVE_CONFIG,
    });
    el.on("afterChange", function (event,slick, current_slide) {
        elm = $(this);
        slide_show = slick.slickGetOption("slidesToShow");
        if (slide_show == 1){
            slide_show = slide_show + 2
        }
        if (el.data('off-after-change')){
            if (current_slide > slick.slideCount - slide_show){
                $(this).find('.slick-next').first().hide();
            }
            else{
                $(this).find('.slick-next').first().show();
            }
        }
        else{
            if (current_slide >= slick.slideCount - slide_show) {
                if (sub_slick[el.selector] == null) {
                    sub_slick[el.selector] = {limit: DEFAULT_NUMBER_SLICK, page: 1, running: 0};
                }
                if (sub_slick[el.selector].running == 0) {
                    sub_slick[el.selector].running = 1;
                    ajax_url = elm.data('url') + "&page=" + (sub_slick[el.selector].page + 1) + "&limit=" + sub_slick[el.selector].limit;
                    $(this).find('.slick-next').first().remove();
                    $(this).append('<button type="button" data-role="none" class="slick-next-loading slick-arrow" aria-label="Next" role="button" aria-disabled="false"><img src="/assets/v1/ellipsis.svg" /></button>');
                    var request = $.ajax({
                        url: ajax_url,
                        data: {container: el.attr('id') , last_id : el.data('last_id')},
                        method: "GET",
                    });
                    request.done(function (msg) {
                        elm.find('.slick-next-loading').first().remove();
                        slick.slickAdd(ajax_render_video_content(msg));
                        sub_slick[el.selector].running = 0;
                        init_like_video_event();
                        init_delete_video_event();
                        if (msg.count == 0) {
                            elm.off('afterChange');
                            /*slick.slickAdd("<div class='card'></div>");*/
                            $(this).find('.slick-next').first().hide();
                            el.on("afterChange", function (event,slick, current_slide) {
                                slide_show = slick.slickGetOption("slidesToShow");
                                if (current_slide > slick.slideCount - slide_show-1){
                                    $(this).find('.slick-next').first().hide();
                                }
                                else{
                                    $(this).find('.slick-next').first().show();
                                }
                            });
                        }
                        else{
                            sub_slick[el.selector].page = sub_slick[el.selector].page + 1;
                        }
                        if (!(msg.last_id == 0)){
                            el.data('last_id',msg.last_id);
                        }
                    });

                    request.fail(function (jqXHR, textStatus) {
                        elm.find('.slick-next-loading').first().remove();
                        console.log(textStatus);
                    });
                }
            }
        }
        return true;
    });
    el.on('edge',function(event,slick, direction){
        console.log(direction);
    });
};