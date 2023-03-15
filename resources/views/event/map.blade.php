<div class="map_image">
    @foreach($map_config->camera as $key=>$item)
        @if($key == $camera_position)
            @php($map_default_name = $item->name)
            <div class="map_spot selected" data-stream='{"url":"{{$item->url}}","name":"{{$item->name}}","key":"{{$key}}"}' onclick="mapSpots(this)" style="left: {{$item->x}}%;top: {{$item->y}}%;"></div>
        @else
            <div class="map_spot" data-stream='{"url":"{{$item->url}}","name":"{{$item->name}}","key":"{{$key}}"}' onclick="mapSpots(this)" style="left: {{$item->x}}%;top: {{$item->y}}%;"></div>
        @endif
    @endforeach
    <img src="{{'/assets/'.'map'.'/'.$map_config->map}}">
</div>
<div class="map_info">
    <div class="map_name">{{$map_default_name}}</div>
    <div class="map_help">Click on dots to change camera angle</div>
</div>