@extends('admin.layout.master')
@section('title','Manage Teams')
@section('main-content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Manage Teams
                <small>Optional description</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Dashboad</a></li>
                <li class="active">Manage setting</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <form method="GET" action="{{route('admin.team')}}">
                    <div class="form-group">
                    <input type="text" name="search" placeholder="team name">
                    <button type="submit">Search</button>
                    </div>
                    </form>
                    <a href="{{route('admin.team.addOrUpdate')}}">
                        <button type="button" class="btn">
                        Add team
                        </button> 
                    </a>
                     {{ $teams->links() }}
                    <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>STT</th>
                        <th>Name</th>
                        <th>Created at</th>
                        <th></th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 0; ?>
                      @foreach($teams as $team)
                      <?php $i++; ?>
                      <tr>
                        <td>{{$i}}</td>
                        <td>{{$team->name}}</td>
                        <td>{{$team->created_at}}</td>
                        <td>
                        <a href="{{route('admin.team.addOrUpdateView',['id' => $team->id])}}"><button type="button" class="btn">
                        Edit
                        </button> </a></td>
                        <td>
                        <a href="{{route('admin.team.delete',['id' => $team->id])}}"><button type="button" class="btn">
                        Delete
                        </button> </a></td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {{ $teams->links() }}
                </div>
            </div>
            <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

