
function validateEmail() 
{
    var flag=true;
    var email=$("#email").val();
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      
   
    if($("#message").val()=="")
    {
        flag=false;

        $("#contact_status").html("Message Not Null");
    }
    if($("#subject").val()=="")
    {
        flag=false;
        $("#contact_status").html("Subject Not Null");
    }
    
    if (!re.test(email))
    {
        flag=false;
        $("#contact_status").html("Email Invaild");
    }   
    if(flag)
    {
        $("#contact_status").css("color", "rgba(134,136,152,1)");
        contact();
    }
    else
    {
          $("#contact_status").css("color", "#bc1c1c");
    }
  
}

$(document).ready(function(){
    $("#email").on('keyup', function (e) {
        if (e.keyCode == 13) {
            validateEmail() ;
        }
    });
    $("#subject").on('keyup', function (e) {
        if (e.keyCode == 13) {
            validateEmail() ;
        }
    });
    $("#message").on('keyup', function (e) {
        if (e.keyCode == 13) {
            validateEmail() ;
        }
    });

    $("#contact_btn").click(function(){
        validateEmail();
    });
});

function contact() {
     $("#contact_status").html("Sending email...");
        $.post(url+"/contact",
                {
                    email:$("#email").val(),
                    subject:$("#subject").val(),
                    message:$("#message").val(),
                },
             function(data){
                
                $("#contact_status").html(data);
             }
         );
}