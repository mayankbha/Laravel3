function loadVideo() {
        //ga('send', 'event', 'Buttons', 'click', 'Load more');
		var offset = $("#page").val();
		console.log(offset);
        $.post(url+"/videos",
            {
                offset:offset
            },
         function(data){
            $('#related_list').append(data);
            $("#page").val(parseInt(offset)+12);
         }
         );
}
