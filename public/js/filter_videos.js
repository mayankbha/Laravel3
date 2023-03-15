var CAROUSEL = 0;
var TRENDING = 1;
var RECENT = 2;
var HIGHLIGHTS = 3;
var VIDEO360 = 4;
var GAME = 5;
$( document ).ready(function() {
       filterVideos();
    });
function filterVideos()
{
   $.ajax({
        beforeSend: function() {
        //$('.waiting').show();
        },
        complete : function() {
            //$('.waiting').hide();
        },
        type : 'POST',
        url  : url + '/filter',
        data :  {
                  filter : CAROUSEL,
                },
        dataType : "json",
        success : function(data) {
           $("#trending-view").html(data.status); 
        }
    });
}