
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];


// When the user clicks on the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
    moveVideoPopup();
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
    moveVideoSmall();
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        moveVideoSmall();
    }
}

function getActionInfo()
{
      setLikeState();
     $.post(url+"/getview",
            {
                vcode:vcode
            },
         function(data){
            $('#view_numb').html(data);

         }
     );
     //end
        
    //get like count
     $.post(url+"/getlike",
        {
            vcode:vcode
        },
        function(data){
            $('#like_numb').html(data);

        }
     );
}