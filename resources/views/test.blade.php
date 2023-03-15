<html>
  <head>
   <script type="text/javascript" src="https://bitmovin-a.akamaihd.net/bitmovin-player/stable/7/bitmovinplayer.js"></script>
  </head>
  <body>
 <div id="bitmovin-player">
</div>

<script type="text/javascript">
window.onload = function() {
var player = typeof bitmovin !== "undefined" ? bitmovin.player("bitmovin-player") : bitdash("bitmovin-player");;
/*var conf = {
key: '0319f92c-6e38-4e73-9336-64089683bb58',
source: {
  //dash: '//bitmovin-a.akamaihd.net/content/playhouse-vr/mpds/105560.mpd',
  hls: 'https://d2540bljzu9e1.cloudfront.net/beta-boomtv/videos-hls/1/EasyBot_Double_b2048_1486635512/EasyBot_Double_b2048_1486635512.mp4.m3u8',
//  progressive: '//d2540bljzu9e1.cloudfront.net/videos-360/720/EasyBot_Double_b.mp4',
  poster: '//bitmovin.com/public-demos/vr-demo/content/poster.jpg',
  vr: {startupMode: '2d',startPosition: 180}
},
style: {aspectratio: '16:9'},
playback : { autoplay: true},
};*/
var conf = {
key: '0319f92c-6e38-4e73-9336-64089683bb58',
source: {
//dash: '//bitmovin-a.akamaihd.net/content/playhouse-vr/mpds/105560.mpd',
//hls:'//bitmovin-a.akamaihd.net/content/playhouse-vr/m3u8s/105560.m3u8',
hls: 'https://beta.boom.tv/hls-video/EasyBot_Double_b2048_1486635512.mp4.m3u8',
//hls: 'https://cloud.beta.boom.tv/beta-boomtv/videos-hls/1/EasyBot_Double_b2048_1486635512/EasyBot_Double_b2048_1486635512.mp4.m3u8',
//pogressive: 'http://bitmovin.com/public-demos/vr-demo/content/playhouse_ios.mp4',
poster: 'https://d2540bljzu9e1.cloudfront.net/beta-boomtv/thumb/68/Counter-Strike__Global_Offensive_2017-02-13_11-54-32_1487015633.jpg',vr: {startupMode: '2d',startPosition: 180}
}
,style: {aspectratio: '2:1'}
};
player.setup(conf).then(function(value) {
 player.setVideoQuality('720_4448000');
console.log('Successfully created bitdash player instance');
}, function(reason) {
console.log('Error while creating bitdash player instance');
console.log(reason);
});
};
</script>


  </body>
</html>


