var vid = document.getElementById("video");
function resizeVideo() {
  var w =$("#video").width();
  var h =$("#video").height(w/RATIO);

}
$( document ).ready(function() {
    $( window ).resize(function() {
      resizeVideo();
    });
    set_next_button_position();
});
vid.onloadeddata =function() {
  resizeVideo();
  console.log(vid.duration);
  countView(vid.duration, vtime);
}