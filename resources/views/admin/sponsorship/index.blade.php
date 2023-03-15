@extends('admin.layout.master')
@section('title','Boom metter')
@section('main-content')
 
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="display:block;overflow:auto;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Sponsorship video
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Sponsorship video</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="col-md-12">
            {{ Form::open(array('url'=>'afkvr-admin/updateSponsorship','files'=>true, 'class' => 'form-horizontal')) }}
                    <input hidden name="user_id" value="{{$user->id}}">
                     <input hidden name="user_code" value="{{$user->code}}">
                    <div class="form-group">
                      <div class="col-sm-2">
                      <label class="control-label" for="video">Video: </label>
                      </div>
                      <div class="col-sm-7">
                        <input type="file" class="form-control" id="video" name="file">
                      </div>
                    </div>
                    <div class="form-group">
                    <div class="col-sm-2">
                      <label class="control-label" for="video">Active time (m/d/y)</label>
                    </div>
                    <div class="col-sm-3">
                          <input type="text" class="form-control" id="starttime" name="starttime"/>
                    </div>
                    <div class="col-sm-1" style="text-align: center;">
                          <span>to</span>
                    </div>
                    <div class="col-sm-3">
                          <input type="text" class="form-control" id="expiretime" name="expiretime"/>
                    </div>
                    </div>
                    <div class="form-group">
                    <div class="col-sm-2">
                      <label class="control-label" for="video"> Timezone </label>
                    </div>
                    <div class="col-sm-3">
                          <!-- <input type="text" class="form-control" id="timezone" name="timezone" value="PST" /> -->
                          <select class="form-control" id="timezone" name="timezone">
                          @foreach($timezones as $key => $tz)
                            <option value="{{$tz}}">{{$tz}}</option>
                          @endforeach
                          </select>
                    </div>
                    </div>
                    </div>
                    @if($errors->any())
                    <div class="alert alert-error">
                    {{$errors->first()}}
                    </div>
                    @endif
                    <div class="col-md-9">
                    <div class="form-group"> 
                      <a href="{{route('admin.boomMeter')}}" class="btn btn-default">Back</a>
                        {{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
                    </div>
                    </div>
                    
                    @if(count($info) > 0)
                        <div class="col-md-9">
                          <div class="panel panel-default">
                            <div class="panel-heading">History</div>
                            @foreach($info as $spon)
                            <div class="panel-body" style="border-bottom: 1px #CCC solid">
                              <p>Link video: <a href="{{$spon['link']}}">{{$spon['link']}}</a></p>
                              <p>Start time: {{$spon['starttime']}}</p>
                              <p>Expired time: {{$spon['stoptime']}}</p>
                              <p>Timezone: {{$spon['timezone']}}</p>
                              <p>Status: {{$spon["status_name"]}}</p>
                              @if($spon["status"] != -1)
                              <a href="{{route('admin.deleteSponsorship',['spon_id' => $spon['id'], 'code' => $user->code])}}"><button type="button" class="btn btn-danger">Delete</button></a>
                              @endif
                            </div>
                            @endforeach
                          </div>
                        </div>
                    @endif
                    
                  {{ Form::close() }}

              </div>
              
        </section>
        <!-- /.content -->
    </div>
    <script type="text/javascript">
         $(function () {
          $('#starttime').datetimepicker({
                minDate : moment()
            });
          $('#expiretime').datetimepicker({
              useCurrent: false //Important! See issue #1075
          });
          $("#starttime").on("dp.change", function (e) {
              $('#expiretime').data("DateTimePicker").minDate(e.date);
          });
          $("#expiretime").on("dp.change", function (e) {
              $('#starttime').data("DateTimePicker").maxDate(e.date);
          });
    });
    </script>
    <!-- /.content-wrapper -->
@endsection

