function signup() {
 $.post(url+"/signup",
        {
            email:$("#email").val(),
            twitch_id:$("#twitch_id").val()
        },
     function(data){
        console.log(data);

     }
 );
}
function validateEmail() 
{
    var flag=true;
    var email=$("#email").val();
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    

    if($("#twitch_id").val()=="")
    {
        flag=false;
        $("#subscribe_error").show();
        $("#subscribe_error").css("color", "#bc1c1c");
        $("#subscribe_error").html("That twitch id is invalid!");
    } 
   

    if (!re.test(email)) {
       flag=false;
        $("#subscribe_error").show();
        $("#subscribe_error").css("color", "#bc1c1c");
        $("#subscribe_error").html("That email is invalid!");
    }
  
    if(flag==true)
    {
        signup();
        $("#subscribe_thanks").show();
        $("#subscribe_thanks").css("color", "rgba(134,136,152,1)");
        $("#subscribe_input").hide();

    }
    else
    {
        $("#subscribe_thanks").hide();
        $("#subscribe_thanks").css("color", "white");
        $("#subscribe_input").show();
    }

}

$(document).ready(function(){
    $("#email").on('keyup', function (e) {
        if (e.keyCode == 13) {
            validateEmail() ;
        }
    });
    $("#twitch_id").on('keyup', function (e) {
        if (e.keyCode == 13) {
            validateEmail() ;
        }
    });


});