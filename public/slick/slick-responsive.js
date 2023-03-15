var main_slick = {limit: 10, page: 1};
var sub_slick = [];

init_main_carousel = function () {
    $('.variable-width-big').slick({
        infinite: false,
        speed: 300,
        slidesToShow: 2,
        centerMode: true,
        variableWidth: true,
        initialSlide: 1,
        responsive: [
            {
                breakpoint: 769,
                settings: {
                    infinite: false,
                    slidesToShow: 1,
                    centerMode: false,
                    variableWidth: false,
                    initialSlide: 1
                }
            }
        ]
    });
    $('.variable-width-big').on("afterChange", function (event, slick, current_slide) {
        if (current_slide == slick.slideCount - 1) {
            x_url = urlFilter + "?filterBy=" + FILTER_CAROUSEL + "&page=" + (main_slick.page + 1) + "&limit=" + main_slick.limit;
            var request = $.ajax({
                url: x_url,
                method: "GET",
            });

            request.done(function (msg) {
                slick.slickAdd(msg.content);
                main_slick.page = main_slick.page + 1;
                if (msg.count == 0){
                    $('.variable-width-big').off('afterChange');
                }

            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });

        }
    });
};

init_el_carousel = function (el, x_url) {
    el.slick({
        infinite: false,
        responsive: [
            {
                breakpoint: 1025,
                settings: {
                    initialSlide: 1
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
            if (sub_slick[el.selector] == null){
                sub_slick[el.selector] = {limit: 10, page: 1};
            }
            ajax_url = x_url + "&page=" + (sub_slick[el.selector].page + 1) + "&limit=" + sub_slick[el.selector].limit;
            var request = $.ajax({
                url: ajax_url,
                method: "GET",
            });

            request.done(function (msg) {
                slick.slickAdd(msg.content);
                sub_slick[el.selector].page = sub_slick[el.selector].page + 1;
                if (msg.count == 0){
                    elm.off('afterChange');
                }

            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });

        }
    });
};