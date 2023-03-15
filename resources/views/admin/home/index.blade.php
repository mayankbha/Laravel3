@extends('admin.layout.master')
@section('title','Afkvr dashboard')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboad
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Here</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-area-chart"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total views</span>
                            <span class="info-box-number">{{number_format($report_data['total_view'])}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-thumbs-o-up"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total likes</span>
                            <span class="info-box-number">{{number_format($report_data['total_like'])}}</span>

                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange-active">
                        <span class="info-box-icon"><i class="fa fa-video-camera"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total vidieos</span>
                            <span class="info-box-number">{{number_format($report_data['total_videos'])}}</span>

                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total streamers who installed</span>
                            <span class="info-box-number">{{$report_data['total_streamer_who_installed']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total streamers who installed since 2017/04/12</span>
                            <span class="info-box-number">{{$report_data['total_streamer_who_installed_rel']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total streamer who uploaded video</span>
                            <span class="info-box-number">{{$report_data['total_streamer_who_uploaded_video']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total streamer who uploaded video since 2017/04/12</span>
                            <span class="info-box-number">{{$report_data['total_streamer_who_uploaded_video_rel']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">streamers have not recorded replay since last 7 days (Churn)</span>
                            <span class="info-box-number">{{$report_data['total_streamer_who_uploaded_video_dont_have_activity_in_subweek']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">List streamers have not recorded replay since last 7 days (Churn)</span>
                            @if($report_data['streamer_no_activity']['storage'] == 'local')
                                <span class="info-box-number"><a
                                            href="{{route('admin.csv-viewer',['file'=>$report_data['streamer_no_activity']['file_name']])}}">View file</a></span>
                            @else
                                <span class="info-box-number"><a
                                            href="{{$report_data['streamer_no_activity']['file_name'] . "?" .\Carbon\Carbon::now()}}">Download file</a></span>
                                <span class="progress-description">
                                    Updated at {{$report_data['streamer_no_activity']['date']}}
                                </span>
                            @endif
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>

            <div class="row">

                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Users uploading video cohort</span>
                            @if($report_data['cohort_user_video']['storage'] == 'local')
                                <span class="info-box-number"><a
                                            href="{{route('admin.csv-viewer',['file'=>$report_data['cohort_user_video']['file_name']])}}">View file</a></span>
                            @else
                                <span class="info-box-number"><a
                                            href="{{$report_data['cohort_user_video']['file_name'] . "?" .\Carbon\Carbon::now()}}">Download file</a></span>
                                <span class="progress-description">
                                    Updated at {{$report_data['cohort_user_video']['date']}}
                                </span>
                            @endif
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Daily active streamers</span>
                            @if($report_data['daily_active_streamers']['storage'] == 'local')
                                <span class="info-box-number"><a
                                            href="{{route('admin.csv-viewer',['file'=>$report_data['daily_active_streamers']['file_name']])}}">View file</a></span>
                            @else
                                <span class="info-box-number"><a
                                            href="{{$report_data['daily_active_streamers']['file_name'] . "?" .\Carbon\Carbon::now()}}">Download file</a></span>
                                <span class="progress-description">
                                    Updated at {{$report_data['daily_active_streamers']['date']}}
                                </span>
                            @endif
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>

            <div class="row">

                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Active Streamers who more than 100 subcribers weekly</span>
                            @if($report_data['cohort_user_video']['storage'] == 'local')
                                <span class="info-box-number"><a
                                            href="{{route('admin.csv-viewer',['file'=>$report_data['weekly_active_streamers']['file_name']])}}">View file</a></span>
                            @else
                                <span class="info-box-number"><a
                                            href="{{$report_data['weekly_active_streamers']['file_name'] . "?" .\Carbon\Carbon::now()}}">Download file</a></span>
                                <span class="progress-description">
                                    Updated at {{$report_data['weekly_active_streamers']['date']}}
                                </span>
                            @endif
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Number of active streamers last day</span>
                            <span class="info-box-number">{{$report_data['number_streamers_using_boom_last_day']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Number of streamers who uploaded replay last day.</span>
                            <span class="info-box-number">{{$report_data['number_streamers_has_replay_last_day']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>
                <!-- /.col -->
            </div>


        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection