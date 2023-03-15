<div class="list-video">
<div class="carousel_title x_hide" id="view-trending-label">Trending</div>
<div class="variable-width"
     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
     id="view-trending">
</div>
<div class="carousel_title x_hide" id="view-recent-label">Recent</div>
<div class="variable-width"
     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
     id="view-recent">

</div>
<div class="carousel_title x_hide" id="view-highlight-label">Highlights</div>
<div class="variable-width"
     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
     id="view-highlight">

</div>
<div class="carousel_title x_hide" id="view-video360-label">360 Videos</div>
<div class="variable-width"
     data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
     id="view-video360">

</div>
@foreach($listgame as $key => $game)
    @if(isset($userGameList[$game->id]))
    <div class="carousel_title x_hide" id="view-game_{{$game->id}}-label">More of {{$game->name}}</div>
    <div class="variable-width game-category"
         data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
         id="view-game_{{$game->id}}">
    </div>
    @endif
@endforeach
</div>