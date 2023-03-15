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
                <div class="col-md-12">
                    <form method="GET" action="{{route('admin.boomMeter')}}">
                    <div class="form-group">
                    <input type="text" name="search" placeholder="user name">
                    <button type="submit">Search</button>
                    </div>
                    </form>
                     {{ $users->links() }}
                    <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>STT</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Boom meter code</th>
                        <th>Created at</th>
                        <th>Use image</th>
                        <th>Sponsorship video</th>
                        <th>Boom meter</th>
                        <th>Boom meter demo</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 0; ?>
                      @foreach($users as $user)
                      <?php $i++; ?>
                      <tr>
                        <td>{{$i}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->code}}</td>
                        <td>{{$user->created_at}}</td>
                        <td>{{$user->getBoomMeterStatus()}}</td>
                        <td>
                        <a href="{{route('admin.setSponsorship',['code' => $user->code])}}"><button type="button" class="btn">
                        Upload
                        </button> </a></td>
                        <td>
                        <a href="{{route('admin.customBoomMeter',['code' => $user->code])}}"><button type="button" class="btn">
                        Upload images 
                        </button> </a></td>
                        <th>
                        <a href="{{route('admin.reviewBoomMeter',['code' => $user->code])}}"><button type="button" class="btn">
                        Demo
                        </button> </a></th>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {{ $users->links() }}
                </div>
            </div>
            <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

