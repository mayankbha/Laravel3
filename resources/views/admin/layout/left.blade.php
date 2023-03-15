<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/admin/dist/img/avatar5.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{auth()->guard('admin')->user()->name}}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        {{--<!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->--}}

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">

            <li @if(\Request::route()->getName() == "admin") class="active" @endif><a href="{{route('admin')}}"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
            {{--<li class="header">Report</li>
            <li @if(\Request::route()->getName() == "admin.dailyActiveStreamers") class="treeview active" @else class="treeview" @endif>
                <a href="#"><i class="fa  fa-bar-chart"></i> <span>System reports</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{route('admin.dailyActiveStreamers')}}">Daily active streamers</a></li>
                </ul>
            </li>--}}
            <li class="header">Event</li>
            <!-- Optionally, you can add icons to the links -->
            <li @if(\Request::route()->getName() == "admin.event") class="active" @endif><a href="{{route('admin.event')}}"><i class="fa fa-link"></i> <span>Config event status</span></a></li>
            <li @if(strpos(\Request::route()->getName(),"admin.event.vod") !== false) class="active" @endif><a href="{{route('admin.event.vod.list')}}"><i class="fa fa-link"></i> <span>Event vod</span></a></li>
            <li @if(strpos(\Request::route()->getName(),"admin.event.setOfUrl") !== false) class="active" @endif><a href="{{route('admin.event.setOfUrl')}}"><i class="fa fa-link"></i> <span>Live event URLs</span></a></li>
            <li class="header">Boom player</li>
            <li @if(\Request::route()->getName() == "admin.setting") class="active" @endif><a href="{{route('admin.setting')}}"><i class="fa fa-link"></i> <span>Boom website setting</span></a></li>
            <li @if(\Request::route()->getName() == "admin.boomMeter") class="active" @endif><a href="{{route('admin.boomMeter')}}"><i class="fa fa-link"></i> <span>Boom meter setting</span></a></li>
            <li @if(\Request::route()->getName() == "admin.team") class="active" @endif><a href="{{route('admin.team')}}"><i class="fa fa-link"></i> <span>Manage Teams</span></a></li>
            <li class="header">Admin tool</li>
            <li @if(\Request::route()->getName() == "admin.csv-difftool") class="active" @endif><a href="{{route('admin.csv-difftool')}}"><i class="fa fa-link"></i> <span>Csv diff tool</span></a></li>
            <li class="header">Churn user reminder</li>
            <li @if(\Request::route()->getName() == "admin.reminder.churn") class="active" @endif><a href="{{route('admin.reminder.churn')}}"><i class="fa fa-link"></i> <span>Churn User Reminder Log</span></a></li>
            <li @if(\Request::route()->getName() == "admin.reminder.emailReport") class="active" @endif><a href="{{route('admin.reminder.emailReport')}}"><i class="fa fa-link"></i> <span>Email report</span></a></li>
            <li @if(\Request::route()->getName() == "admin.reminder.userComeback") class="active" @endif><a href="{{route('admin.reminder.userComeback')}}"><i class="fa fa-link"></i> <span>Report User Comeback</span></a></li>
            {{--<li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
            {{--<li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
            <li class="treeview">
                <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#">Link in level 2</a></li>
                    <li><a href="#">Link in level 2</a></li>
                </ul>
            </li>--}}
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>