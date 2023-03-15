@extends('admin.layout.master')
@section('title','Event status')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Event config
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Event config</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-9">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Event status Form</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">


                        </div>

                        <!-- form start -->
                        <form class="form-horizontal" method="post">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Current event status</label>

                                    <div class="col-sm-9">
                                        <select name="game_status" class="form-control">
                                            @foreach($map_array as $key=>$item)
                                            <option @if($game_status == $key) selected="selected" @endif value="{{$key}}">Event online with {{$item}} map</option>
                                            @endforeach
                                            <option @if($game_status == 1000) selected="selected" @endif value="1000">Event offline</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$next_event_date->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$next_event_date->title}}" name="next_event_date" value="{{$next_event_date->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('game_name')->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$boom_setting->get('game_name')->title}}" name="{{$boom_setting->get('game_name')->name}}" value="{{$boom_setting->get('game_name')->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('team_name')->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$boom_setting->get('team_name')->title}}" name="{{$boom_setting->get('team_name')->name}}" value="{{$boom_setting->get('team_name')->value}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('allow_ip_list')->title}}</label>
                                    <div class="col-sm-9">
                                        <textarea name="{{$boom_setting->get('allow_ip_list')->name}}" class="form-control" rows="3" placeholder="Delimiter by ,">{{$boom_setting->get('allow_ip_list')->value}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('event_map_change')->title}}</label>
                                    <div class="col-sm-9">

                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="{{$boom_setting->get('event_map_change')->name}}" id="optionsRadios1" value="automatic"
                                                    @if($boom_setting->get('event_map_change')->value == 'automatic') checked="checked" @else @endif />
                                                Automatic
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="{{$boom_setting->get('event_map_change')->name}}" id="optionsRadios1" value="manual" @if($boom_setting->get('event_map_change')->value == 'manual') checked="checked" @else @endif>
                                                Manual
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Coming soon event</label>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('comingsoon_date')->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$boom_setting->get('comingsoon_date')->title}}" name="{{$boom_setting->get('comingsoon_date')->name}}" value="{{$boom_setting->get('comingsoon_date')->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('comingsoon_game_name')->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$boom_setting->get('comingsoon_game_name')->title}}" name="{{$boom_setting->get('comingsoon_game_name')->name}}" value="{{$boom_setting->get('comingsoon_game_name')->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$boom_setting->get('comingsoon_team_name')->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$boom_setting->get('comingsoon_team_name')->title}}" name="{{$boom_setting->get('comingsoon_team_name')->name}}" value="{{$boom_setting->get('comingsoon_team_name')->value}}">
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <button type="button" onclick="window.location = '{{redirect()->back()->getTargetUrl()}}'" class="btn btn-default">Back</button>
                                <button type="button" id="event_submit" class="btn btn-info pull-right">Update</button>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                    </div>
                </div>
            </div>
            <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@push('page-javascript')
<script type="text/javascript">
    var success_msg = '<div class="alert alert-success alert-dismissible ">'+
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
    '<span>Success update</span>'+
    '</div>';
    var error_msg = '<div class="alert alert-error alert-dismissible">'+
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
    '<span>Error update</span>'+
    '</div>';
    $(function () {
        $('input[name="comingsoon_date"]').datepicker({
            "format": 'yyyy-mm-dd',
            "autoclose": true
        });
    });
    $(document).ready(function(){
        $("#event_submit").click(function(e){
            $('#msg_bar').html("");
            e.preventDefault();
            var event_status = $("select[name='game_status'] option:selected").val();
            var next_event_date = $("input[name='next_event_date']").val();
            var team_name = $("input[name='team_name']").val();
            var game_name = $("input[name='game_name']").val();
            var event_map_change = $("input[name='event_map_change']:checked").val();
            var allow_ip_list = $("textarea[name='allow_ip_list']").val();
            var comingsoon_date = $('input[name="comingsoon_date"]').val();
            var comingsoon_game_name = $('input[name="comingsoon_game_name"]').val();
            var comingsoon_team_name = $('input[name="comingsoon_team_name"]').val();

            var request = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $("input[name='_token']").val(),
                },
                url: "",
                data : {
                    game_status : event_status,
                    next_event_date : next_event_date,
                    game_name : game_name,
                    team_name : team_name,
                    event_map_change : event_map_change,
                    allow_ip_list : allow_ip_list,
                    comingsoon_date : comingsoon_date,
                    comingsoon_team_name : comingsoon_team_name,
                    comingsoon_game_name : comingsoon_game_name,
                },
                method: "POST",
            });

            request.done(function (msg) {
                $('#msg_bar').html(success_msg);
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(textStatus);
            });
        });
        $("#success_alert_button").off('click');
        $("#error_alert_button").off('click');
    });

</script>
@endpush
