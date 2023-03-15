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
                <li class="active">Manage Teams</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                @if($isEdit)
                <h3>Edit team</h3>
                @else
                <h3>Add new a team</h3>
                @endif
                </div>
                <div class="col-md-12">
                {{ Form::open(array('files'=>true, 'class' => 'form-horizontal', 'id' => 'form-team')) }}
                {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{$teamId}}">
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Team name</label>
                      <div class="col-sm-11">
                        <input type="text" class="form-control" id="teamname" name="teamname" value="{{$teamname}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <div class="panel panel-default">
                          <div class="panel-heading">Users (Choose a user to set admin of team)</div>
                          <input type="hidden" id="users_team" name="users_team" value="{{$usersTeam}}">
                          <div class="panel-body" id = "list_users_team">
                          </div>
                          <div class="panel-body" id = "list_add_users_team">
                          @if($users != null)
                            @foreach($users as $user)
                            <div class='col-sm-3'>
                              <span class='delete-user'
                  onclick='removeUser(this)'>&nbsp &otimes; &nbsp</span><span>
                                <span>{{$user->name}}</span> 
                                <input type='radio' name='is_owner'
                                 value="{{$user->name}}"
                                @if($ownername == $user->name)
                                  checked="checked"
                                @endif
                                >
                            </div>
                            @endforeach
                          @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-2">
                        <input type="text" class="form-control" id="search_user" name="search_user" placeholder="search user">
                      </div>
                      <div class="col-sm-2">
                        <span class="btn btn-default" onclick="addTeam();">Add team</span>
                      </div>
                    </div>
                    <div style="height: 20px"></div>
                    @if($banner != "")
                    <div>
                        <img width="600" height="264" src="{{$banner}}">
                    </div>
                    <div style="height: 20px"></div>
                    @endif
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Banner image: </label>
                      <div class="col-sm-11">
                        <input type="file" class="form-control" id="images" name="banner">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Twitch link: </label>
                      <div class="col-sm-11">
                        <input type="text" class="form-control" id="twitch_link" name="twitch_link" value="{{$links['twitch_link']}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Website: </label>
                      <div class="col-sm-11">
                        <input type="text" class="form-control" id="website" name="website" value="{{$links['website']}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Twitter link: </label>
                      <div class="col-sm-11">
                        <input type="text" class="form-control" id="twitter_link" name="twitter_link" value="{{$links['twitter_link']}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-sm-1" for="name">Facebook link: </label>
                      <div class="col-sm-11">
                        <input type="text" class="form-control" id="facebook_link" name="facebook_link" value="{{$links['facebook_link']}}">
                      </div>
                    </div>
                    @if($errors->any())
                    <div class="alert alert-error">
                    {{$errors->first()}}
                    </div>
                    @endif
                    </div>
                    <div class="form-group"> 
                      <div class=" col-sm-10">
                      <a href="{{route('admin.team')}}" class="btn btn-default">Back</a>
                        {{ Form::submit('Save', array('class' => 'btn btn-primary', 'id' => 'submit_form')) }}
                      </div>
                    </div>
                  {{ Form::close() }}
                </div>
            <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <script type="text/javascript">
    $(document).ready(function(){
      $('body').on('keydown', 'input, select, textarea', function(e) {
          var self = $(this)
            , form = self.parents('form:eq(0)')
            , focusable
            , next
            ;
          if (e.keyCode == 13) {
              focusable = form.find('input,a,select,button,textarea').filter(':visible');
              next = focusable.eq(focusable.index(this)+1);
              if (next.length) {
                  next.focus();
              } else {
                  form.submit();
              }
              return false;
          }
      });
        var url = "{{url('')}}";

        $('#search_user').autocomplete({ source:url+"/afkvr-admin/team/searchUser"});

        $("#teamname").blur(function() {
            var teamname = $("#teamname").val();
            console.log('out ' + teamname);
            $.post(url+"/afkvr-admin/team/getUsersTeam",
                    {
                        teamname:teamname,
                        _token:$("input[name=_token]").val()
                    },
                 function(data){
                      if(data.status == 0 && data.users.length > 0)
                      {
                        $("#list_users_team").html("");
                        var dataUser = data.users;
                        $.each(dataUser, function(i, val){
                          var html = "<div class='col-sm-3'> \
                          <span class='delete-user' \
              onclick='removeUser(this)'>&nbsp &otimes; &nbsp</span><span>\
                            <span>"+dataUser[0].name+"</span> \
                            <input type='radio' name='is_owner'\
                             value='"+dataUser[0].name+"'></div>";
                          $("#list_users_team").append(html);
                      });
                        addUsersTeam();
                      }
                      else
                      {
                        $("#list_users_team").html("Not found user twitch for team");
                      }
                      $("#twitch_link").val("https://www.twitch.tv/team/"+teamname);
                 }
            );
        });
    });

    function addTeam()
    {
      var name = $('#search_user').val();
      var html = "<div class='col-sm-3'> \
      <span class='delete-user' onclick='removeUser(this)'>&nbsp &otimes; &nbsp</span><span>"+name+"</span> \
      <input type='radio' name='is_owner' value='"+name+"'></div>";
      $("#list_add_users_team").append(html); 
      addUsersTeam();
    }

    function addUsersTeam()
    {
      var listUser = "";
      $("input[name='is_owner']").each(function(){
         listUser += $(this).val()+",";
      });
      listUser = listUser.substring(0,listUser.length-1);
      $("#users_team").val(listUser); 
    }

    function removeUser(event)
    {
      $(event).parent().remove();
      addUsersTeam();
    }
    </script>
@endsection

