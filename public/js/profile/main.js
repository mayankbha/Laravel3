/**
 * Created by tannn on 3/14/17.
 */
$(document).ready(function(){
    var snackbar = "snackbar";
    $(".user_context_edit").on('click',function(e){
        $(this).hide();

        setTimeout(function(){$("input:text:visible:first").focus();},1);
        $(".user_context_save").show().css('display','flex');

        if ($(".profile_game_accounts").hasClass("x_hide")){
            $(".profile_game_accounts").removeClass("x_hide");
        }
        else{

        }
        if ($(".game_accounts_link").hasClass("x_hide")){
            $(".game_accounts_link").removeClass("x_hide");
        }
        else{

        }
        $(".game_accounts_account").hide();
        $(".game_accounts_input").show();

        $(".user_context_about").hide();
        $(".user_context_about_input").show();

        $(".user_context_social").hide();
        $(".user_context_social_input").show();
    });

    $(".user_context_save").on('click',function(e){
        $(this).hide();
        $(".user_context_edit").show().css('display','flex');;


        var request = $.ajax({
            url: "/myprofile",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $("input[name='csrf-token']").val(),
            },
            data : {
                steam:$("input[name='steam']").val(),
                battle:$("input[name='battle']").val(),
                lol : $("input[name='lol']").val(),
                des : $("textarea[name='des']").val(),
                twitter : $("input[name='twitter']").val(),
                reddit : $("input[name='reddit']").val(),
                facebook : $("input[name='facebook']").val(),
            },
        });
        request.done(function (data,textStatus,xhr) {
            if (data.state){
                show_msg(data.msg, snackbar);
                my_user = $.parseJSON(data.user);
                game_acount_status = 0;
                $(".game_accounts_account").each(function(i,item){
                    object_user_name = $(item).parent().find('input').first().attr('name');
                    if (my_user[object_user_name]){
                        $(item).html(my_user[object_user_name]);
                        $(item).attr('title',my_user[object_user_name]);
                        $(item).parent().removeClass("x_hide");
                        game_acount_status = 1;
                    }
                    else{
                        $(item).parent().addClass("x_hide");
                    }
                });
                if (game_acount_status){
                    $(".profile_game_accounts").removeClass("x_hide");
                }
                else{
                    $(".profile_game_accounts").addClass("x_hide");
                }
                $(".user_context_about").html(my_user.des);

                if (my_user.twitter_link){
                    $(".user_context_social[name='twitter_link']").attr('href',generate_twitter_follow(my_user.twitter_link));
                    $(".user_context_social[name='twitter_link']").removeClass("x_hide");
                    $(".user_context_social[name='twitter_link']").show();
                }
                if (my_user.facebook_link){
                    $(".user_context_social[name='facebook_link']").attr('href',generate_facebook_follow(my_user.facebook_link));
                    $(".user_context_social[name='facebook_link']").removeClass("x_hide");
                    $(".user_context_social[name='facebook_link']").show();
                }
                if (my_user.reddit_link){
                    $(".user_context_social[name='reddit_link']").attr('href',generate_reddit_follow(my_user.reddit_link));
                    $(".user_context_social[name='reddit_link']").removeClass("x_hide");
                    $(".user_context_social[name='reddit_link']").show();
                }



            }
            else{
                $(".game_accounts_account").each(function(i,item){
                    $(item).parent().find('input').first().val($(item).html());
                });
                $(".user_context_about").parent().find('textarea').first().val($(".user_context_about").html());
            }
        });

        request.fail(function (jqXHR, textStatus) {
            if (jqXHR.status == 422){
                var errors = jqXHR.responseJSON;
                mess = "Updated failse. Following errors :<br/>";

                $.each( errors , function( key, value ) {
                    mess += '<li>' + value[0] + '</li>'; //showing only the first error.
                });
                mess += '</ul></di>';
                show_msg(mess, snackbar);
            }
        });

        $(".game_accounts_account").show();
        $(".game_accounts_input").hide();

        $(".user_context_about").show();
        $(".user_context_about_input").hide();

        $(".user_context_social_input").hide();
    });

});