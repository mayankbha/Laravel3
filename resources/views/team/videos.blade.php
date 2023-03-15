<div class="list-video">
@foreach($members as $key => $user)
    @if($user->user->countVideos() >= 1)
    <div class="carousel_title x_hide" id="view-user_{{$user->user_id}}-label">
    {{$user->user->displayname}}'s videos
    </div>
    <div class="variable-width user-category"
         data-slick='{"infinite": false, "speed": 300, "slidesToShow": 5, "variableWidth": true, "initialSlide": 0}'
         id="view-user_{{$user->user_id}}">
    </div>
    @endif
@endforeach
</div>