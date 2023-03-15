@extends('admin.layout.master')
@section('title','Csv viewer')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Csv viewer
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Csv viewer</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">{{$file_name}}</h3>

                            <div class="box-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <div class="input-group-btn">
                                        <a class="btn btn-default" target="_blank" href="{{route('admin.csv-download',['file'=>$file_name])}}"><i class="fa fa-download"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                @foreach($data as $key=>$user)
                                    @if($key == 0)
                                        <tr>
                                            @foreach($user as $item)
                                                <th>{{$item}}</th>
                                            @endforeach
                                        </tr>
                                    @else
                                        <tr>
                                            @foreach($user as $item)
                                                <td>{{$item}}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
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