<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.include.admin.style')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('style')
</head>

<body>
    <div id="app" class="position-relative">
        @include('layouts.include.admin.header')
        @include('layouts.include.admin.sidebar')

        <div class="app-content content ">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            @yield('content')
        </div>
        {{-- <main class="ptc-100 pbc-50">
            @yield('content')
        </main> --}}
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</body>

@include('layouts.include.admin.script')
@stack('script')

</html>
