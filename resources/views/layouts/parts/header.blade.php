<nav id="navbar">
    <a href="{!! route('home') !!}" class="brand-wrapper">
        <img class="logo" src="{!! url('/') !!}/images/logo.png" alt="logo">
    </a>

    <div class="account-wrapper">
        @if(Auth::guest())
            {{--<a class="link" href="{!! url('/login') !!}">
                <span class="icon-circle"><i class="fa fa-sign-in"></i></span> Login
            </a>--}}
        @else
            <span class="icon-circle"><i class="fa fa-user"></i></span> {!! \Auth::user()->name !!}

            <ul class="account-dropdown">
                <li class="item">
                    <a href="{!! url('/logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> Logout
                    </a>
                </li>
            </ul>

            <form id="logout-form" action="{!! url('/logout') !!}" method="POST" class="hidden">
                {{ csrf_field() }}
            </form>
        @endif
    </div>



</nav>