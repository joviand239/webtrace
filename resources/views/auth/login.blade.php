@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="login-wrapper">
            <div class="login-box">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="control-label">Username</label>

                        <input id="username" type="username" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                        @if ($errors->has('username'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="control-label">Password</label>

                        <input id="password" type="password" class="form-control" name="password" required>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}> Remember Me
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="text-center">
                            <button type="submit" class="btn">
                                Login
                            </button>

                            {{-- <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                 Forgot Your Password?
                             </a>--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection
