/**
 * Created by tannn on 3/16/17.
 */
get_and_set_category_slide = function(x_url,el){
    el.data('url',x_url);
    var request = $.ajax({
        url: x_url,
        data : {container:el.attr('id')},
        method: "GET",
    });

    request.done(function (msg) {
        el.html(ajax_render_video_content(msg));
        init_el_carousel(el,x_url);
        init_like_video_event();
        init_delete_video_event();
        if (msg.count > 0){
            $("#"+msg.container).removeClass("x_hide");
            $("#"+msg.container+"-label").removeClass("x_hide");
        }
    });

    request.fail(function (jqXHR, textStatus) {
        console.log(textStatus);
    });
};


get_and_set_game_slide = function(i_x){
    item = $('.game-category:eq('+game_category_iterator[i_x]+')');
    if (item.length > 0){
        game_category_iterator[i_x] = game_category_iterator[i_x] + game_category_iterator.length;
        var gameid = item.attr('id').split('_')[1];
        x_url = url_video_user_filer + "&filterBy=" + FILTER_GAME + "&gameId=" + gameid + "&json=1";
        item.data('url',x_url);
        var request = $.ajax({
            url: x_url,
            data : {container:item.attr('id')},
            method: "GET",
        });

        request.done(function (msg) {
            $("#view-game_"+msg.gameId).html(ajax_render_video_content(msg));
            init_like_video_event();
            init_el_carousel($("#view-game_"+msg.gameId),x_url);
            init_delete_video_event();
            if (msg.count > 0){
                $("#"+msg.container).removeClass("x_hide");
                $("#"+msg.container+"-label").removeClass("x_hide");
            }
            setTimeout(function(){get_and_set_game_slide(i_x)},0);

        });

        request.fail(function (jqXHR, textStatus) {
            console.log(textStatus);
        });
    }

};

x_url = url_video_user_filer + "&filterBy=" + FILTER_TRENDING + "&json=1";
get_and_set_category_slide(x_url,$("#view-trending"));
x_url = url_video_user_filer + "&filterBy=" + FILTER_RECENT + "&json=1";
get_and_set_category_slide(x_url,$("#view-recent"));
x_url = url_video_user_filer + "&filterBy=" + FILTER_HIGHLIGHTS + "&json=1";
get_and_set_category_slide(x_url,$("#view-highlight"));
x_url = url_video_user_filer + "&filterBy=" + FILTER_VIDEO360 + "&json=1";
get_and_set_category_slide(x_url,$("#view-video360"));

var game_category_iterator = [0,1,2];
get_and_set_game_slide(0);
get_and_set_game_slide(1);
get_and_set_game_slide(2);