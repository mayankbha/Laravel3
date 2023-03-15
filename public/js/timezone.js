function gettimezone()
{
	var offset = new Date().getTimezoneOffset();
	var minutes = Math.abs(offset);
	var hours = Math.floor(minutes / 60);
	var prefix = offset <= 0 ? "+" : "-";
	var timezone = prefix+hours;
	$.post(url+"/set_userzone",
            {
                user_zone:timezone
            },
         function(data){
         	 
           if(data==false)
            location.reload();
           else
            console.log("Timezone detected");
           

         }
    );
}

$(document).ready(function(){
    gettimezone();
});