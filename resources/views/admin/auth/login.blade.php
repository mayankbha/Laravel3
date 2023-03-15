@extends('admin.layout.login')
@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('admin.login')}}"><b>Afkvr</b>admin</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="{{route('admin.login')}}" method="post">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
                {{ csrf_field() }}
            </form>

            <div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="{{route('login-to-afkvr-admin')}}" class="btn btn-block btn-social btn-flat bg-orange color-palette"><i class="fa fa-bold"></i>Login from Boom web</a>
            </div>
            <!-- /.social-auth-links -->

            {{--<a href="#">I forgot my password</a><br>
            <a href="register.html" class="text-center">Register a new membership</a>--}}

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection