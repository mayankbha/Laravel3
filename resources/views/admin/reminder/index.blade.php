@extends('admin.layout.master')
@section('title','Reminder Log')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Reminder Log
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Reminder Log</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Reminder Log</h3>
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
                            <form action="{{route("admin.reminder.churn")}}" method="get">
                                <div class="input-group">
                                    <input type="text" name="s" placeholder="Search by name"
                                          @if(isset($input_data['s'])) value="{{$input_data['s']}}" @endif class="form-control">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-success btn-flat">Search</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <div class="box-header clearfix">
                            {{$list_reminder->links()}}
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody><tr>
                                    <th>Reminder ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>User Created At</th>
                                    <th>Last Video Created At</th>
                                    <th>Number Video</th>
                                    <th>Current Status</th>
                                    <th>Time zone</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($list_reminder as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->user->name}}</td>
                                        <td>{{$item->email}}</td>
                                        <td>{{$item->user_created_at}}</td>
                                        <td>{{$item->last_video_created_at}}</td>
                                        <td>{{$item->number_video}}</td>
                                        <td>{{$item->currentStatusToString()}}</td>
                                        <td>{{$item->timezone}}</td>
                                        <td>
                                            @if($item->current_status == \App\Models\UserReminder::CURRENT_STATUS_START_REMIND)
                                                <a href="{{route('admin.reminder.start',['id'=>$item->id])}}" class="btn bg-navy margin">Start Reminder</a>
                                            @endif
                                                <a data-href="{{route('admin.reminder.getEmailLog',['id'=>$item->id])}}" data-toggle="modal" data-target="#emailLogModal" class="btn bg-navy margin">Get Email Log</a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer clearfix">
                            {{$list_reminder->links()}}
                        </div>

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
@push('page-javascript')
<script>
    $('#emailLogModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var href = button.data('href') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this);
        var request = $.ajax({
            url: href,
            method: "GET",
        });

        request.done(function (msg) {
            modal.find('.modal-body').html(msg.content)
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(textStatus);
        });
    })
</script>
@endpush