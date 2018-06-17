<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
@include('includes.head')
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}"><i class="fa fa-home" style="margin-right: 4px;"></i>Home</a>
                    @else
                        <a href="{{ route('login') }}"><i class="far fa-user" style="margin-right: 4px;"></i>Login</a>
                        <a href="{{ route('register') }}"><i class="fas fa-user-plus" style="margin-right: 4px;"></i>Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md animated infinite bounce">
                    Jet Production <i class="far fa-heart pov"></i>
                </div>

                <div class="links">
                    <a href="/"><i class="fab fa-vk fa-lg"></i></a>
                    <a href="/Servers"><i class="fas fa-server" style="margin-right: 4px;"></i>Servers</a>
                    <a href="/rules"><i class="fas fa-book-open" style="margin-right: 4px;"></i>Rules</a>
                    <a href="/download"><i class="fas fa-download" style="margin-right: 4px;"></i>Download</a>
                    <a href="/about"><i class="fa fa-info-circle" style="margin-right: 4px;"></i>About</a>
                </div>
            </div>
        </div>
    </body>
</html>
