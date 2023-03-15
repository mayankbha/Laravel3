function checkVideo() {
	setTimeout(function(){
	  
       $.post(url+"/api/checkVideo",
            {
                vcode:vcode
            },
         function(data){
         	 
            var data_decode=JSON.parse(data);

            if(data_decode['status']==1)
            {
            	console.log(data_decode['message']);

            	$("#video_loading").hide();
            	$("#video_hls").html(hls_data);
                
            
                VideoInit();
                
            	
            }
            else
            {
            	console.log(data_decode['error']);
				$("#video_loading").show();
            	// $("#video_hl_data").hide();
            	
            	checkVideo();

            }
         }
         );
       
    },5000);
}
function checkVideoJW() {
    setTimeout(function(){
      
       $.post(url+"/api/checkVideo",
            {
                vcode:vcode
            },
         function(data){
             
            var data_decode=JSON.parse(data);

            if(data_decode['status']==1)
            {
                console.log(data_decode['message']);

                $("#video_loading").hide();
                $("#init_jw").html(jw_data);
                
            
                VideoInit();
                
                
            }
            else
            {
                console.log(data_decode['error']);
                $("#video_loading").show();
                // $("#video_hl_data").hide();
                
                checkVideoJW();

            }
         }
         );
       
    },5000);
}
