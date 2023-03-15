@extends('admin.layout.master')
@section('title','Boom metter')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Boom metter
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Boom metter</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-6">
                <h3>Custom boom meter for "{{$user->name}}"</h3>
                </div>
                <div class="col-md-6">
                <h3>&nbsp&nbsp&nbspClick image to view details</h3>
                </div>
                <div class="col-md-6">
                {{ Form::open(array('url'=>'afkvr-admin/uploadImageBoomMeter','files'=>true, 'class' => 'form-horizontal')) }}
                    <div class="form-group">
                      <label class="control-label col-sm-2" for="code">Code</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="code" name="code" value="{{$code}}" readonly="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-sm-2" for="images">Images zip: </label>
                      <div class="col-sm-10">
                        <input type="file" class="form-control" id="images" name="file">
                      </div>
                    </div>
                    @if($errors->any())
                    <div class="alert alert-error">
                    {{$errors->first()}}
                    </div>
                    @endif
                    <div class="alert alert-warning">
                      <strong>Note: </strong> <br/>
                      Files name in zip file: {{implode(", ", $images)}}<br/>
                    </div>
                    <div class="form-group"> 
                      <div class=" col-sm-10">
                      <a href="{{route('admin.boomMeter')}}" class="btn btn-default">Back</a>
                        {{ Form::submit('Upload and review', array('class' => 'btn btn-primary')) }}
                      </div>
                    </div>

                  {{ Form::close() }}
                </div>
                <div class="col-md-6">
                  @foreach($images as $img)
                  <div class="col-md-2">
                    <div class="thumbnail">
                        <a href="{{$links3.$img}}" target="_blank">
                        <img src="{{$links3.$img}}" alt="Nature" style="width:50%">
                        <div class="caption">
                          <p>{{$img}}</p>
                        </div>
                        </a>
                    </div>
                  </div>
                  @endforeach
            </div>
            <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

