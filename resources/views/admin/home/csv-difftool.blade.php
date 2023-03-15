@extends('admin.layout.master')
@section('title','Csv viewer')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Csv diff tool
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Csv diff tool</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Csv diff tool upload file</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">
                            @if(isset($msg))
                                <div class="alert alert-error alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-check"></i> Alert!</h4>
                                    {{$msg}}
                                </div>
                            @endif
                        </div>
                        <div class="box-body">

                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('admin.csv-difftool')}}" >
                                {{csrf_field()}}
                                <div class="box-body">

                                    <div class="form-group">
                                        <label for="exampleInputFile">File input</label>
                                        <input type="file" name="file" id="exampleInputFile">

                                        <p class="help-block">Select file </p>
                                    </div>
                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Get diff</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection