/**
 * Created by tannn on 3/16/17.
 */



get_and_set_game_slide = function(i_x){
    item = $('.user-category:eq('+game_category_iterator[i_x]+')');
    if (item.length > 0){
        game_category_iterator[i_x] = game_category_iterator[i_x] + game_category_iterator.length;
        var userid = item.attr('id').split('_')[1];
        x_url = url_video_user_filer + "?uid="+userid+"&filterBy=" + FILTER_USER + "&json=1&teamId="+teamId;
        item.data('url',x_url);
        var request = $.ajax({
            url: x_url,
            data : {container:item.attr('id')},
            method: "GET",
        });

        request.done(function (msg) {
            $("#view-user_"+msg.userId).html(ajax_render_video_content(msg));
            init_el_carousel($("#view-user_"+msg.userId),x_url);
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

var game_category_iterator = [0,1,2];
get_and_set_game_slide(0);
get_and_set_game_slide(1);
get_and_set_game_slide(2);