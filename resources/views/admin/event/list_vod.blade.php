@extends('admin.layout.master')
@section('title','Event status')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                List Event vod
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">List Event vod</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">List Event vod</h3>
                            <div class="box-tools">
                                <div class="input-group input-group-sm" style="width: 150px">
                                    <div class="input-group-btn">
                                        <a href="{{route('admin.event.vod.add')}}"  class="btn btn-block btn-primary btn-flat"><i class="fa fa-plus"></i> Add event vod</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="msg_bar">

                            @if (session()->has('vod_msg'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <span>{{session()->get('vod_msg')}}</span>
                                </div>
                            @endif
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody><tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Jumbotron url</th>
                                    <th>Game name</th>
                                    <th>Team name</th>
                                    <th>Map name</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($data['list_vod'] as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->jumbotron_url}}</td>
                                        <td>{{$item->game_name}}</td>
                                        <td>{{$item->team_name}}</td>
                                        <td>{{$item->map_name}}</td>
                                        <td>
                                            <a href="{{route('admin.event.vod.edit',['id'=>$item->id])}}" class="btn bg-navy margin">Edit</a>
                                            <a data-href="{{route('admin.event.vod.remove',['id'=>$item->id,'back'=>url()->current()])}}" href="#" class="btn bg-navy margin afkvr-admin-remove">Remove</a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
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
