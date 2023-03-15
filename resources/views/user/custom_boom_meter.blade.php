@extends('layouts.master')
@section('title', 'Customize your Boom Meter')
@section('ogtype', 'article')
@section('ogurl', '')
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <main>
        <div class="gradient">
            <div class="background"></div>
        </div>
        <div class="boom_meter_title">Customize your Boom Meter
            <div class="link_name"><span class="boom_meter_fun">Note:</span> {{$note}}
            </div>
        </div>

        <div class="boom_meter_list">
            @foreach($boomMeterTypes as $boomMeter)
                @if($boomMeter->type != $boomMeter::DEFAULT_LOCK_TYPE)
                    <div class="boom_meter_item">
                        <div class="boom_meter_preview">
                            @if($boomMeter->type == $boomMeter::CUSTOM_TYPE)
                                @if($hasImage)
                                    <img class="boom_meter_image"
                                         src="{{$imageCustom.$boomMeter->image.'?'.$timestamp}}">
                                @else

                                @endif
                            @else
                                <img class="boom_meter_image"
                                     src="{{$links3.$boomMeter->folders3.'/'.$boomMeter->image.'?'.$boomMeter->version}}">
                            @endif
                            <img class="boom_locked" src="{{$assetLink}}locked.png">
                            @if($boomMeter->type == 2)
                                @if($hasImage)
                                    <a target="_blank" class=""
                                       href="{{route('demo_boom_meter',['id'=>$boomMeter->id])}}">
                                        <ul class="boom_meter_card_play">
                                            <li class="boom_meter_play_circle one"></li>
                                            <li class="boom_meter_play_circle two"></li>
                                            <li class="boom_meter_play_triangle"></li>
                                        </ul>
                                    </a>
                                @endif
                            @else
                                <a target="_blank" class=""
                                   href="{{route('demo_boom_meter',['id'=>$boomMeter->id])}}">
                                    <ul class="boom_meter_card_play">
                                        <li class="boom_meter_play_circle one"></li>
                                        <li class="boom_meter_play_circle two"></li>
                                        <li class="boom_meter_play_triangle"></li>
                                    </ul>
                                </a>
                            @endif
                        </div>

                        <div class="boom_meter_about">
                            <div class="boom_meter_name">
                                @if($boomMeter->name == "Custom")
                                    <a href="{{route("review_boom_meter")}}">{{$boomMeter->name}}</a>
                                @else
                                    {{$boomMeter->name}}
                                @endif
                                @if($boomMeter->type == $boomMeter::CUSTOM_TYPE)
                                    <a href="{{route('custom_boom_meter')}}" class="boom_meter_modal_btn">Upload</a>
                                @endif
                            </div>
                            @if($boomMeter->type != $boomMeter::CUSTOM_TYPE || ($boomMeter->type == $boomMeter::CUSTOM_TYPE && $hasImage))
                                @if($boomMeter->id == $installTypeId)
                                    <a class="boom_meter_get_button_x skin_button_select" data-href="{{route('action_boom_meter', ['action' => 'install','boom_meter_id' => $boomMeter->id,'ref'=>$ref])}}">
                                        <div>
                                            <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/check-mark-16.png'}}">
                                        </div>
                                        SELECTED
                                    </a>
                                @else
                                    <a class="boom_meter_get_button"
                                       data-href="{{route('action_boom_meter', ['action' => 'install','boom_meter_id' => $boomMeter->id,'ref'=>$ref])}}">
                                        GET
                                    </a>
                                @endif
                            @endif

                            <a class="boom_meter_fun unlock" onclick="popupState(this)">Unlock</a>
                        </div>
                    </div>
                @else
                    {{--<div class="boom_meter_item locked">
                    <div class="boom_meter_preview">
                        <img class="boom_meter_image" src="{{$linkImageDefault.$boomMeter->getName().'/'.$boomMeter->getName().'.png'}}">
                        <img class="boom_locked" src="{{$assetLink}}locked.png">
                    </div>
                    <div class="boom_meter_about">
                        <div class="boom_meter_name">{{$boomMeter->name}}</div>
                        <a class="boom_meter_fun install">Install</a>
                        <a class="boom_meter_fun unlock" onclick="popupState(this)">Unlock</a>
                    </div>
                    <div class="boom_meter_modal_container">
                        <div class="boom_meter_modal">
                            <div class="boom_meter_modal_video">
                                <img src="{{$assetLink}}modal-video-1.png">
                                <img class="boom_meter_modal_meter" src="{{$linkImageDefault.$boomMeter->getName().'/'.$boomMeter->getName().'.png'}}">
                            </div>
                            <div class="boom_meter_modal_context">
                                <div class="boom_meter_modal_about">
                                    <div class="boom_meter_modal_title">{{$boomMeter->name}}</div>
                                    <div class="boom_meter_modal_body">Here’s what you need to do to unlock this wicked skin:</div>
                                    <div class="boom_meter_modal_task">Follow BoomTV on Twitter</div>
                                    <div class="boom_meter_modal_task">Tag your bros that would enjoy using BoomTV in one of our tweets or Facebook posts</div>
                                </div>
                                <img class="boom_meter_modal_locked" src="{{$assetLink}}locked.png">
                                <a class="boom_meter_modal_close" onclick="popupState(this)"><img src="{{$assetLink}}modal-close.png"></a>
                                <div class="boom_meter_modal_btn_holder">
                                    <a class="boom_meter_modal_btn" href="#">Click here when you’re done</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
                @endif
            @endforeach


        </div>

    </main>
    <div class="modal_bg">
    </div>
    <div class="modal_two" style="display: none">
        <div class="modal_container">
            <div class="modal_content">
                <a class="modal_function" href="javascript:hide_modal();">
                    <img src="/assets/v1/modal-close.png">
                </a>
                <div class="modal_two_title">Note</div>
                <div class="modal_two_main">Content</div>
                <div class="modal_two_buttons" style="position: relative;height: 29px">
                    <a class="boom_meter_popout_button" style="right: 0;position: absolute" href="javascript:hide_modal();">OK</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push("content-javascript")
<style>
</style>
{!! Html::script(config('content.cloudfront').'/jquery-confirm/3.2/dist/jquery-confirm.min.js') !!}
{!! Html::style(config('content.cloudfront').'/jquery-confirm/3.2/dist/jquery-confirm.min.css') !!}
<script>
    var boom_mete_check_mark_img = '{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/check-mark-16.png'}}';
    $(document).ready(function () {
        trigger_selected_event();
    });
</script>
{!! Html::script('/js/v1/boom-meter.js?v=201705191') !!}
@endpush