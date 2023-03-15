@extends('admin.layout.master')
@section('title','User Comeback')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                User Comeback
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">User Comeback</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">User Comeback</h3>
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody><tr>
                                    <th>User ID</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Follow number</th>
                                    <th>Start Sent Mail</th>
                                    <th>Last Sent Mail</th>
                                    <th>Times sent mail</th>
                                    <th>Comeback Day</th>
                                </tr>
                                @foreach($datas as $items)
                                    <tr bgcolor="#1A2226" style="color: #FFF">
                                    <td colspan="8">Start time {{$items["start"]}}
                                     - End time {{$items["end"]}}
                                    </td>
                                    </tr>
                                    @foreach($items["data"] as $item)
                                    <tr>
                                        <td>{{$item->user_id}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->email}}</td>
                                        <td>{{$item->follower_numb}}</td>
                                        <td>{{$item->first_sent_at}}</td>
                                        <td>{{$item->getLastSendMail()}}</td>
                                        <td>{{$item->current_template}}</td>
                                        <td>{{$item->updated_at}}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
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
    <!-- Modal -->
    <div class="modal fade" id="emailLogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Email Log</h4>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection