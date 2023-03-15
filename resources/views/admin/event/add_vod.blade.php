@extends('admin.layout.master')
@section('title','Add vod')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Add vod
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Add vod</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-9">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Event vod</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>

                        <!-- form start -->
                        <form class="form-horizontal" method="post" action="{{route('admin.event.vod.add')}}"
                              enctype="multipart/form-data">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Map name</label>

                                    <div class="col-sm-9">
                                        <select name="map_id" class="form-control">
                                            @foreach($data['map_name_array'] as $key=>$item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="name" name="name" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Game name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="Game name" name="game_name"
                                               value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Team name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="Team name" name="team_name"
                                               value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Vod date</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="Vod date" id="vod_date"
                                               name="vod_date" value=""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Thumbnail</label>
                                    <div class="col-sm-9">

                                        <div class="input-group input-group-sm" id="thumbnail_input"
                                             style="visibility:visible">
                                            <input type="file" name="file_thumb">
                                        </div>
                                        <input type="hidden" name="update_thumbnail" value="1">

                                    </div>
                                </div>

                                @foreach($list_live_urls as $key=>$item)
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Set url {{$key}}</label>

                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Jumbotron url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumbotron url" name="{{$key}}[]" value="{{$item->jumbotron}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Caster url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Caster url" name="{{$key}}[]" value="{{$item->caster}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 1 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 1 url" name="{{$key}}[]" value="{{$item->live_1}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 2 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 2 url" name="{{$key}}[]" value="{{$item->live_2}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 3 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 3 url" name="{{$key}}[]" value="{{$item->live_3}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 4 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 4 url" name="{{$key}}[]" value="{{$item->live_4}}">
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                {{csrf_field()}}
                                <button type="button" onclick="window.location = '{{route("admin.event.vod.list")}}'"
                                        class="btn btn-default">Back
                                </button>
                                <button type="submit" id="event_submit" class="btn btn-info pull-right">Add</button>
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
    $(function () {
        $('#vod_date').datepicker({
            "format": 'yyyy-mm-dd',
            "autoclose": true
        });
    });
    delete_current_thumb = function () {
        $("input[name='update_thumbnail']").val(1);
        $("#thumbnail_input").css("visibility", 'visible');
        $("#thumbnail_value").css("visibility", 'hidden');
    }
    undo_current_thumb = function () {
        $("input[name='update_thumbnail']").val(0);
        $("#thumbnail_input").css("visibility", 'hidden');
        $("#thumbnail_value").css("visibility", 'visible');
    }
</script>
@endpush
