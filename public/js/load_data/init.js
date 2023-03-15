get_and_set_category_slide = function(x_url,el){
    var success_ajax = function(msg){
        el.html(ajax_render_video_content(msg));
        init_el_carousel(el);
        init_like_video_event();
        init_delete_video_event();
        el.data('last_id',msg.last_id);
        build_list_category_game();
    };

    var fail_ajax = function(obj_ajax){
        obj_ajax.tryCount++;
        if (obj_ajax.tryCount < obj_ajax.retryLimit) {
            retry_ajax = $.ajax(obj_ajax);
            retry_ajax.done(function (msg) {
                success_ajax(msg);
            });
            retry_ajax.fail(function (jqXHR, textStatus) {
                fail_ajax(this);
            });
        }
        return;
    };

    el.data('url',x_url)
    var request = $.ajax({
        url: x_url,
        data : {container:el.attr('id'),last_id : el.data('last_id')},
        method: "GET",
        tryCount : 0,
        retryLimit : 3,
        el_container : el,
    });

    request.done(function (msg) {
        success_ajax(msg);
    });

    request.fail(function (jqXHR, textStatus) {
        console.log(textStatus);
        fail_ajax(this);
    });
};

get_and_set_main_slide = function(x_url,el){
    if (el.length){
        var request = $.ajax({
            url: x_url,
            method: "GET",
        });

        request.done(function (msg) {
            el.html(msg.content);
            init_main_carousel(el);
            init_like_main_video_event();
            init_delete_video_event();
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(textStatus);
        });
    }
};

get_and_set_game_slide = function(i_x){
    var ajax_success = function(msg){
        $("#view-game_"+msg.gameId).html(ajax_render_video_content(msg));
        init_like_video_event();
        init_el_carousel($("#view-game_"+msg.gameId));
        init_delete_video_event();
        setTimeout(function(){get_and_set_game_slide(i_x)},0);
        $("#view-game_"+msg.gameId).data('last_id',msg.last_id);
    };

    var fail_ajax = function(obj_ajax){
        obj_ajax.tryCount++;
        if (obj_ajax.tryCount < obj_ajax.retryLimit) {
            retry_ajax = $.ajax(obj_ajax);
            retry_ajax.done(function (msg) {
                success_ajax(msg);
            });
            retry_ajax.fail(function (jqXHR, textStatus) {
                fail_ajax(this);
            });
        }
        return;
    };

    item = $('.game-category:eq('+game_category_iterator[i_x]+')');
    if (item.length > 0){
        game_category_iterator[i_x] = game_category_iterator[i_x] + game_category_iterator.length;
        var gameid = item.attr('id').split('_')[1];
        x_url = urlFilter + "?filterBy=" + FILTER_GAME + "&gameId=" + gameid + "&json=1";
        item.data('url', x_url);
        var request = $.ajax({
            url: x_url,
            data : {container:item.attr('id')},
            method: "GET",
            tryCount : 0,
            retryLimit : 3,
        });

        request.done(function (msg) {
            ajax_success(msg);
        });

        request.fail(function (jqXHR, textStatus) {
            fail_ajax(this);
        });
    }
};

get_and_set_game_slide_x = function(i_x){
    game_category_is_loaded[i_x].is_loaded = 1;
    $('#view-game_'+game_category_is_loaded[i_x].game_id).html('<img style="width: 50px;" width="50" src="/assets/v1/ellipsis.svg" />');
    var ajax_success = function(msg){
        $("#view-game_"+msg.gameId).html(ajax_render_video_content(msg));
        init_like_video_event();
        init_el_carousel($("#view-game_"+msg.gameId));
        init_delete_video_event();
        $("#view-game_"+msg.gameId).data('last_id',msg.last_id);
        build_list_category_game();
    };

    var fail_ajax = function(obj_ajax){
        obj_ajax.tryCount++;
        if (obj_ajax.tryCount < obj_ajax.retryLimit) {
            retry_ajax = $.ajax(obj_ajax);
            retry_ajax.done(function (msg) {
                success_ajax(msg);
            });
            retry_ajax.fail(function (jqXHR, textStatus) {
                fail_ajax(this);
            });
        }
        return;
    };
    if ($)
    item = $('.game-category:eq('+i_x+')');
    if (item.length > 0){
        var gameid = item.attr('id').split('_')[1];
        x_url = urlFilter + "?filterBy=" + FILTER_GAME + "&gameId=" + gameid + "&json=1";
        item.data('url', x_url);
        var request = $.ajax({
            url: x_url,
            data : {container:item.attr('id')},
            method: "GET",
            tryCount : 0,
            retryLimit : 3,
        });

        request.done(function (msg) {
            ajax_success(msg);
        });

        request.fail(function (jqXHR, textStatus) {
            fail_ajax(this);
        });
    }
};

build_list_category_game = function(){
    game_category_title = [];
    $('.carousel_title').each(function (index,obj) {
        if ($(obj).data('id') != null){
            game_category_title.push({
                game_id : $(obj).data('id'),
                current_top : $(obj).offset().top,
            });
        }
    })
}

build_list_category_game_is_loaded = function () {
    $('.carousel_title').each(function (index,obj) {
        if ($(obj).data('id') != null){
            game_category_is_loaded.push({
                game_id : $(obj).data('id'),
                is_loaded : 0,
            });
        }
    })
}
var last_check_category = 0;
check_scroll_and_load_data = function(){
    scroll_top = $(window).scrollTop() + $(window).height() -100;
    for (i = last_check_category;i < game_category_title.length;i++){
        if (game_category_title[i].current_top > scroll_top){
            break;
        }
    }
    for (j=last_check_category;j < i;j++){
        if (game_category_is_loaded[j].is_loaded == 0){
            get_and_set_game_slide_x(j);
        }
    }
    last_check_category = i;
}

init_main_carousel();
//get_and_set_main_slide(urlFilter + "?filterBy=" + FILTER_CAROUSEL,$("#view-carousel"));
init_like_main_video_event();



x_url = urlFilter + "?filterBy=" + FILTER_TRENDING + "&json=1";
$("#view-trending").data('url',x_url);
init_el_carousel($("#view-trending"),x_url);
$("#view-trending2").data('url',x_url);
init_el_carousel($("#view-trending2"),x_url);
init_like_video_event();
x_url = urlFilter + "?filterBy=" + FILTER_RECENT + "&json=1";
get_and_set_category_slide(x_url,$("#view-recent"));
x_url = urlFilter + "?filterBy=" + FILTER_HIGHLIGHTS + "&json=1";
get_and_set_category_slide(x_url,$("#view-highlight"));
x_url = urlFilter + "?filterBy=" + FILTER_VIDEO360 + "&json=1";
get_and_set_category_slide(x_url,$("#view-video360"));

var first_scroll = 1;
var game_category_iterator = [0,1,2];
var game_category_title = [];
var game_category_is_loaded = [];
/*get_and_set_game_slide(0);
get_and_set_game_slide(1);
get_and_set_game_slide(2);*/

$(document).ready(function(){
    init_delete_video_event();
    build_list_category_game();
    build_list_category_game_is_loaded();
    check_scroll_and_load_data();
    $(window).scroll(function () {
        check_scroll_and_load_data();
    })
});


