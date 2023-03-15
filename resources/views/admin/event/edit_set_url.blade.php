@extends('admin.layout.master')
@section('title','Live event URLs')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Live event URLs
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Live event URLs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-9">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Live event URLs config</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">


                        </div>

                        <!-- form start -->
                        <form class="form-horizontal" method="post" action="{{route('admin.event.setOfUrl')}}" enctype="multipart/form-data">
                            <div class="box-body">
                                @foreach($data as $item)
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">{{$item->title}}</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Jumbotron url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumbotron url" name="{{$item->name}}[]" value="{{$item->jumbotron}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Caster url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Caster url" name="{{$item->name}}[]" value="{{$item->caster}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 1 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 1 url" name="{{$item->name}}[]" value="{{$item->live_1}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 2 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 2 url" name="{{$item->name}}[]" value="{{$item->live_2}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 3 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 3 url" name="{{$item->name}}[]" value="{{$item->live_3}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Live 4 url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Live 4 url" name="{{$item->name}}[]" value="{{$item->live_4}}">
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                {{csrf_field()}}
                                <button type="button" onclick="window.location = '{{route("admin")}}'" class="btn btn-default">Back</button>
                                <button type="submit" id="event_submit" class="btn btn-info pull-right">Update</button>
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
</script>
@endpush
