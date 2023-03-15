get_and_set_image_slide = function(){
    item = $('.channel-category:eq('+image_category_iterator+')');
    if (item.length > 0){
        image_category_iterator ++;
        var channelId = item.attr('id').split('_')[1];
        x_url = urlImageFilter + "?filterBy=" + IMG_FILTER_CHANNEL + "&channelId=" + channelId;
        item.data('url',x_url);
        var request = $.ajax({
            url: x_url,
            method: "GET",
        });

        request.done(function (msg) {
            $("#image-channel_"+msg.channelId).html(msg.content);
            init_like_image_event();
            init_el_carousel($("#image-channel_"+msg.channelId),x_url);
            get_and_set_image_slide();
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(textStatus);
        });
    }

};
var image_category_iterator = 0;
get_and_set_image_slide();
init_like_main_video_event();


