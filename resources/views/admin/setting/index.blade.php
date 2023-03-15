@extends('admin.layout.master')
@section('title','Boom website config')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Boom website setting
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Boom website setting</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Event status Form</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">
                            @if($setting_msg)
                                <div class="alert alert-{{$setting_msg['status'][0]}} alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-check"></i> Alert!</h4>
                                    {{$setting_msg['msg'][0]}}
                                </div>
                            @endif
                        </div>

                        <!-- form start -->
                        <form class="form-horizontal" method="post" action="{{route('admin.setting')}}">
                            <div class="box-body">
                                @foreach($settings as $item)
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">{{$item->title}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="{{$item->title}}" name="value[]" value="{{$item->value}}">
                                        <input type="hidden" name="name[]" value="{{$item->name}}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                {{csrf_field()}}
                                <button type="button" onclick="window.location = '{{redirect()->back()->getTargetUrl()}}'" class="btn btn-default">Back</button>
                                <button type="submit" class="btn btn-info pull-right">Update</button>
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
